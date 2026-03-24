<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Application\Manager;

use GuzzleHttp\Exception\GuzzleException;
use Hanaboso\CommonsBundle\Enum\ApplicationTypeEnum;
use Hanaboso\CommonsBundle\Transport\Curl\CurlException;
use Hanaboso\CommonsBundle\Transport\Curl\CurlManager;
use Hanaboso\PipesPhpSdk\Application\Base\ApplicationAbstract;
use Hanaboso\PipesPhpSdk\Application\Base\ApplicationInterface;
use Hanaboso\PipesPhpSdk\Application\Document\ApplicationInstall;
use Hanaboso\PipesPhpSdk\Application\Exception\ApplicationInstallException;
use Hanaboso\PipesPhpSdk\Application\Loader\ApplicationLoader;
use Hanaboso\PipesPhpSdk\Application\Manager\Webhook\WebhookApplicationInterface;
use Hanaboso\PipesPhpSdk\Application\Manager\Webhook\WebhookManager;
use Hanaboso\PipesPhpSdk\Application\Repository\ApplicationInstallFilter;
use Hanaboso\PipesPhpSdk\Application\Repository\ApplicationInstallRepository;
use Hanaboso\PipesPhpSdk\Authorization\Base\Basic\BasicApplicationInterface;
use Hanaboso\PipesPhpSdk\Authorization\Base\OAuth1\OAuth1ApplicationInterface;
use Hanaboso\PipesPhpSdk\Authorization\Base\OAuth2\OAuth2ApplicationInterface;
use Hanaboso\Utils\Exception\DateTimeException;
use Hanaboso\Utils\System\PipesHeaders;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApplicationManager
 *
 * @package Hanaboso\PipesPhpSdk\Application\Manager
 */
final class ApplicationManager
{

    public const string APPLICATION_SETTINGS = 'applicationSettings';

    /**
     * ApplicationManager constructor.
     *
     * @param ApplicationInstallRepository $applicationInstallRepository
     * @param ApplicationLoader            $loader
     * @param WebhookManager               $webhook
     */
    public function __construct(
        protected readonly ApplicationInstallRepository $applicationInstallRepository,
        protected ApplicationLoader $loader,
        private readonly WebhookManager $webhook,
    )
    {
    }

    /**
     * @return mixed[]
     */
    public function getApplications(): array
    {
        return $this->loader->getApplications();
    }

    /**
     * @param string $key
     *
     * @return ApplicationInterface
     * @throws ApplicationInstallException
     */
    public function getApplication(string $key): ApplicationInterface
    {
        return $this->loader->getApplication($key);
    }

    /**
     * @param string $key
     *
     * @return string[]
     * @throws ApplicationInstallException
     */
    public function getSynchronousActions(string $key): array
    {
        $actions    = [];
        $reflection = new ReflectionClass($this->getApplication($key));
        $methods    = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if ($this->isSynchronous($method)) {
                $actions = array_merge($actions, [$method->getName()]);
            }
        }

        return $actions;
    }

    /**
     * @param string  $key
     * @param string  $method
     * @param Request $request
     *
     * @return mixed
     * @throws ApplicationInstallException
     */
    public function runSynchronousAction(string $key, string $method, Request $request): mixed
    {
        $app = $this->getApplication($key);

        if (method_exists($app, $method)) {
            if ($request->getMethod() === CurlManager::METHOD_GET) {
                return $app->$method();
            }

            return $app->$method($request);
        }

        throw new ApplicationInstallException(
            sprintf('Method "%s" was not found for Application "%s".', $method, $key),
            ApplicationInstallException::METHOD_NOT_FOUND,
        );
    }

    /**
     * @param string  $key
     * @param string  $user
     * @param string  $sdk
     * @param mixed[] $data
     *
     * @return mixed[]
     * @throws ApplicationInstallException
     * @throws GuzzleException
     */
    public function saveApplicationSettings(string $key, string $user, string $sdk, array $data): array
    {
        /** @var BasicApplicationInterface $application */
        $application        = $this->loader->getApplication($key);
        $applicationInstall = $this->applicationInstallRepository->findUserApp($key, $user, $sdk);
        $res                = $application->saveApplicationForms($applicationInstall, $data)->toArray();
        $this->applicationInstallRepository->update($applicationInstall);

        return array_merge(
            $res,
            [self::APPLICATION_SETTINGS => $application->getApplicationForms($applicationInstall)],
        );
    }

    /**
     * @param string $key
     * @param string $user
     * @param string $sdk
     * @param string $formKey
     * @param string $fieldKey
     * @param string $password
     *
     * @return ApplicationInstall
     * @throws ApplicationInstallException
     * @throws GuzzleException
     */
    public function saveApplicationPassword(
        string $key,
        string $user,
        string $sdk,
        string $formKey,
        string $fieldKey,
        string $password,
    ): ApplicationInstall
    {
        $applicationInstall = $this->applicationInstallRepository->findUserApp($key, $user, $sdk);

        /** @var BasicApplicationInterface $application */
        $application = $this->loader->getApplication($key);
        $application = $application->savePassword($applicationInstall, $formKey, $fieldKey, $password);
        $this->applicationInstallRepository->update($applicationInstall);

        return $application;
    }

    /**
     * @param string $key
     * @param string $user
     * @param string $sdk
     * @param string $redirectUrl
     *
     * @return string
     * @throws ApplicationInstallException
     * @throws GuzzleException
     */
    public function authorizeApplication(string $key, string $user, string $sdk, string $redirectUrl): string
    {
        $applicationInstall = $this->applicationInstallRepository->findUserApp($key, $user, $sdk);

        /** @var OAuth1ApplicationInterface|OAuth2ApplicationInterface $application */
        $application = $this->loader->getApplication($key);
        $application->setFrontendRedirectUrl($applicationInstall, $redirectUrl);
        $this->applicationInstallRepository->update($applicationInstall);

        return $application->authorize($applicationInstall);
    }

    /**
     * @param string  $key
     * @param string  $user
     * @param string  $sdk
     * @param mixed[] $token
     *
     * @return string
     * @throws ApplicationInstallException
     * @throws GuzzleException
     */
    public function saveAuthorizationToken(string $key, string $user, string $sdk, array $token): string
    {
        $applicationInstall = $this->applicationInstallRepository->findUserApp($key, $user, $sdk);

        /** @var OAuth1ApplicationInterface|OAuth2ApplicationInterface $application */
        $application = $this->loader->getApplication($key);
        $application->setAuthorizationToken($applicationInstall, $token);
        $this->applicationInstallRepository->update($applicationInstall);

        return $application->getFrontendRedirectUrl($applicationInstall);
    }

    /**
     * @param string $user
     * @param string $sdk
     *
     * @return mixed[]
     * @throws GuzzleException
     */
    public function getInstalledApplications(string $user, string $sdk): array
    {
        return $this->applicationInstallRepository->findMany(
            new ApplicationInstallFilter(users: [$user], sdks: [$sdk]),
        );
    }

    /**
     * @param string $key
     * @param string $user
     * @param string $sdk
     *
     * @return ApplicationInstall
     * @throws ApplicationInstallException
     * @throws GuzzleException
     */
    public function getInstalledApplicationDetail(string $key, string $user, string $sdk): ApplicationInstall
    {
        return $this->applicationInstallRepository->findUserApp($key, $user, $sdk);
    }

    /**
     * @param string $key
     * @param string $user
     * @param string $sdk
     *
     * @return ApplicationInstall
     * @throws ApplicationInstallException
     * @throws GuzzleException
     */
    public function installApplication(string $key, string $user, string $sdk): ApplicationInstall
    {
        $existing = $this->applicationInstallRepository->findOne(
            new ApplicationInstallFilter(names: [$key], users: [$user], sdks: [$sdk]),
        );

        if ($existing) {
            throw new ApplicationInstallException(
                sprintf('Application [%s] was already installed.', $key),
                ApplicationInstallException::APP_ALREADY_INSTALLED,
            );
        }

        $applicationInstall = new ApplicationInstall();
        $applicationInstall
            ->setUser($user)
            ->setKey($key)
            ->setSdk($sdk);
        $this->applicationInstallRepository->insert($applicationInstall);

        return $applicationInstall;
    }

    /**
     * @param string $key
     * @param string $user
     * @param string $sdk
     *
     * @return ApplicationInstall
     * @throws ApplicationInstallException
     * @throws CurlException
     * @throws GuzzleException
     */
    public function uninstallApplication(string $key, string $user, string $sdk): ApplicationInstall
    {
        $applicationInstall = $this->applicationInstallRepository->findUserApp($key, $user, $sdk);
        $this->unsubscribeWebhooks($applicationInstall, $sdk);

        $this->applicationInstallRepository->remove($applicationInstall);

        return $applicationInstall;
    }

    /**
     * @param ApplicationInstall $applicationInstall
     * @param string             $sdk
     * @param mixed[]            $data
     *
     * @return void
     * @throws ApplicationInstallException
     * @throws CurlException
     * @throws DateTimeException
     * @throws GuzzleException
     */
    public function subscribeWebhooks(ApplicationInstall $applicationInstall, string $sdk, array $data = []): void
    {
        /** @var WebhookApplicationInterface $application */
        $application = $this->loader->getApplication($applicationInstall->getKey() ?? '');

        if (ApplicationTypeEnum::isWebhook($application->getApplicationType()) &&
            $application->isAuthorized($applicationInstall)
        ) {
            $this->webhook->subscribeWebhooks($application, $applicationInstall->getUser() ?? '', $sdk, $data);
        }
    }

    /**
     * @param ApplicationInstall $applicationInstall
     * @param string             $sdk
     * @param mixed[]            $data
     *
     * @return void
     * @throws ApplicationInstallException
     * @throws CurlException
     * @throws GuzzleException
     */
    public function unsubscribeWebhooks(ApplicationInstall $applicationInstall, string $sdk, array $data = []): void
    {
        /** @var WebhookApplicationInterface $application */
        $application = $this->loader->getApplication($applicationInstall->getKey() ?? '');

        if (ApplicationTypeEnum::isWebhook($application->getApplicationType()) &&
            $application->isAuthorized($applicationInstall)
        ) {
            $this->webhook->unsubscribeWebhooks($application, $applicationInstall->getUser() ?? '', $sdk, $data);
        }
    }

    /**
     * @param string $key
     * @param string $user
     * @param string $sdk
     *
     * @return mixed[]
     * @throws ApplicationInstallException
     * @throws GuzzleException
     */
    public function getApplicationSettings(string $key, string $user, string $sdk): array
    {
        $applicationInstall = $this->applicationInstallRepository->findUserApp($key, $user, $sdk);
        /** @var ApplicationAbstract $application */
        $application = $this->loader->getApplication($key);

        return $application->getApplicationForms($applicationInstall);
    }

    /**
     * @param string  $user
     * @param string  $sdk
     * @param mixed[] $applications
     *
     * @return mixed[]
     * @throws GuzzleException
     */
    public function getApplicationsLimits(string $user, string $sdk, array $applications): array
    {
        $applicationInstalls = $this->applicationInstallRepository->findUserApps($user, $applications, $sdk);

        $appLimits = array_map(static function(ApplicationInstall $appInstall) use ($sdk) {
            $limiterForm = $appInstall->getSettings()[ApplicationInterface::LIMITER_FORM] ?? NULL;
            if (!$limiterForm) {
                return NULL;
            }

            $useLimit = $limiterForm[ApplicationInterface::USE_LIMIT] ?? NULL;
            $time     = $limiterForm[ApplicationInterface::TIME] ?? NULL;
            $value    = $limiterForm[ApplicationInterface::VALUE] ?? NULL;

            $groupTime  = $limiterForm[ApplicationInterface::GROUP_TIME] ?? NULL;
            $groupValue = $limiterForm[ApplicationInterface::GROUP_VALUE] ?? NULL;

            if (!$useLimit || !$time || !$value) {
                return NULL;
            }

            if($groupTime && $groupValue){
                return PipesHeaders::getLimiterKeyWithGroup(
                    sprintf('%s|%s:%s', $appInstall->getUser(), $sdk, $appInstall->getKey()),
                    (int) $time,
                    (int) $value,
                    sprintf('|%s:%s', $sdk, $appInstall->getKey()),
                    (int) $groupTime,
                    (int) $groupValue,
                );
            }

            return PipesHeaders::getLimiterKey(
                sprintf('%s|%s:%s', $appInstall->getUser(), $sdk, $appInstall->getKey()),
                (int) $time,
                (int) $value,
            );
        }, $applicationInstalls);

        // @phpstan-ignore-next-line
        return array_filter($appLimits);
    }

    /**
     * @param string $key
     * @param string $user
     * @param string $sdk
     * @param bool   $enabled
     *
     * @return ApplicationInstall
     * @throws ApplicationInstallException
     * @throws GuzzleException
     */
    public function changeStateOfApplication(string $key, string $user, string $sdk, bool $enabled): ApplicationInstall
    {
        $applicationInstall = $this->applicationInstallRepository->findUserApp($key, $user, $sdk);
        $applicationInstall->setEnabled($enabled);

        $this->applicationInstallRepository->update($applicationInstall);

        return $applicationInstall;
    }

    /**
     * @param ReflectionMethod $method
     *
     * @return bool
     */
    private function isSynchronous(ReflectionMethod $method): bool {
        $doc = $method->getDocComment();
        preg_match_all('#@SynchronousAction#s', $doc ?: '', $annotations);

        return $annotations[0] !== [];
    }

}
