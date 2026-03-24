<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\HbPFApplicationBundle\Handler;

use GuzzleHttp\Exception\GuzzleException;
use Hanaboso\CommonsBundle\Enum\ApplicationTypeEnum;
use Hanaboso\CommonsBundle\Transport\Curl\CurlException;
use Hanaboso\PipesPhpSdk\Application\Document\ApplicationInstall;
use Hanaboso\PipesPhpSdk\Application\Exception\ApplicationInstallException;
use Hanaboso\PipesPhpSdk\Application\Manager\ApplicationManager;
use Hanaboso\PipesPhpSdk\Application\Manager\Webhook\WebhookApplicationInterface;
use Hanaboso\PipesPhpSdk\Application\Manager\Webhook\WebhookManager;
use Hanaboso\PipesPhpSdk\Application\Model\CustomAction\CustomAction;
use Hanaboso\PipesPhpSdk\Authorization\Base\Basic\BasicApplicationAbstract;
use Hanaboso\PipesPhpSdk\Authorization\Base\Basic\BasicApplicationInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

/**
 * Class ApplicationHandler
 *
 * @package Hanaboso\PipesPhpSdk\HbPFApplicationBundle\Handler
 */
final class ApplicationHandler
{

    private const string SYNC_METHODS         = 'syncMethods';
    private const string AUTHORIZED           = 'authorized';
    private const string ENABLED              = 'enabled';
    private const string WEBHOOK_SETTINGS     = 'webhookSettings';
    private const string APPLICATION_SETTINGS = 'applicationSettings';
    private const string CUSTOM_ACTIONS       = 'customActions';

    /**
     * ApplicationHandler constructor.
     *
     * @param ApplicationManager $applicationManager
     * @param WebhookManager     $webhookManager
     */
    public function __construct(
        private readonly ApplicationManager $applicationManager,
        private readonly WebhookManager $webhookManager,
    )
    {
    }

    /**
     * @return mixed[]
     * @throws ApplicationInstallException
     */
    public function getApplications(): array
    {
        return [
            'items' => array_map(
                fn(string $key): array => $this->applicationManager->getApplication($key)->toArray(),
                $this->applicationManager->getApplications(),
            ),
        ];
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
        return $this->applicationManager->getApplicationsLimits($user, $sdk, $applications);
    }

    /**
     * @param string $key
     *
     * @return mixed[]
     * @throws ApplicationInstallException
     */
    public function getApplicationByKey(string $key): array
    {
        return array_merge(
            $this->applicationManager->getApplication($key)->toArray(),
            [
                self::SYNC_METHODS => $this->applicationManager->getSynchronousActions($key),
            ],
        );
    }

    /**
     * @param string $key
     *
     * @return mixed[]
     * @throws ApplicationInstallException
     */
    public function getSynchronousActions(string $key): array
    {
        return $this->applicationManager->getSynchronousActions($key);
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
        return $this->applicationManager->runSynchronousAction($key, $method, $request);
    }

    /**
     * @param string $user
     * @param string $sdk
     *
     * @return mixed[]
     * @throws ApplicationInstallException
     * @throws GuzzleException
     */
    public function getApplicationsByUser(string $user, string $sdk): array
    {
        return [
            'items' => array_map(
                function (ApplicationInstall $applicationInstall): array {
                    $key        = $applicationInstall->getKey();
                    $authorized = FALSE;

                    try {
                        $application = $this->applicationManager->getApplication($key ?? '');
                        $authorized  = $application->isAuthorized($applicationInstall);
                    } catch (Throwable) {
                    }

                    return array_merge(
                        $applicationInstall->toArray(),
                        [
                            self::AUTHORIZED => $authorized,
                        ],
                    );
                },
                $this->applicationManager->getInstalledApplications($user, $sdk),
            ),
        ];
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
    public function getApplicationByKeyAndUser(string $key, string $user, string $sdk): array
    {
        /** @var BasicApplicationAbstract&WebhookApplicationInterface $application */
        $application        = $this->applicationManager->getApplication($key);
        $applicationInstall = $this->applicationManager->getInstalledApplicationDetail($key, $user, $sdk);

        return array_merge(
            $application->toArray(),
            [
                self::APPLICATION_SETTINGS => $application->getApplicationForms($applicationInstall),
                self::AUTHORIZED           => $application->isAuthorized($applicationInstall),
                self::CUSTOM_ACTIONS       => $this->customActionsToArray($application->getCustomActions()),
                self::ENABLED              => $applicationInstall->isEnabled(),
                self::WEBHOOK_SETTINGS     => $application->getApplicationType() === ApplicationTypeEnum::WEBHOOK->value
                    ? $this->webhookManager->getWebhooks($application, $user, $sdk)
                    : [],

            ],
        );
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
    public function installApplication(string $key, string $user, string $sdk): array
    {
        /** @var BasicApplicationAbstract $application */
        $application        = $this->applicationManager->getApplication($key);
        $applicationInstall = $this->applicationManager->installApplication($key, $user, $sdk);

        return array_merge(
            $application->toArray(),
            [
                self::APPLICATION_SETTINGS => $application->getApplicationForms($applicationInstall),
                self::AUTHORIZED           => $application->isAuthorized($applicationInstall),
            ],
        );
    }

    /**
     * @param string $key
     * @param string $user
     * @param string $sdk
     *
     * @return mixed[]
     * @throws ApplicationInstallException
     * @throws CurlException
     * @throws GuzzleException
     */
    public function uninstallApplication(string $key, string $user, string $sdk): array
    {
        return array_merge(
            $this->applicationManager->uninstallApplication($key, $user, $sdk)->toArray(),
            [
                self::APPLICATION_SETTINGS => NULL,
                self::AUTHORIZED           => FALSE,
            ],
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
    public function updateApplicationSettings(string $key, string $user, string $sdk, array $data): array
    {
        return $this->applicationManager->saveApplicationSettings($key, $user, $sdk, $data);
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
    public function updateApplicationPassword(string $key, string $user, string $sdk, array $data): array
    {
        if (!array_key_exists(BasicApplicationInterface::PASSWORD, $data)) {
            throw new InvalidArgumentException('Field password is not included.');
        }

        if (!array_key_exists('formKey', $data)) {
            throw new InvalidArgumentException('Field formKey is not included.');
        }

        if (!array_key_exists('fieldKey', $data)) {
            throw new InvalidArgumentException('Field fieldKey is not included.');
        }

        return $this->applicationManager->saveApplicationPassword(
            $key,
            $user,
            $sdk,
            $data['formKey'],
            $data['fieldKey'],
            $data[BasicApplicationInterface::PASSWORD],
        )->toArray();
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
        return $this->applicationManager->authorizeApplication($key, $user, $sdk, $redirectUrl);
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
    public function saveAuthToken(string $key, string $user, string $sdk, array $token): string
    {
        return $this->applicationManager->saveAuthorizationToken($key, $user, $sdk, $token);
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
        return $this->applicationManager->changeStateOfApplication($key, $user, $sdk, $enabled);
    }

    /**
     * @param CustomAction[] $customActions
     *
     * @return mixed[]
     */
    private function customActionsToArray(array $customActions): array
    {
        $arr = [];
        foreach ($customActions as $action) {
            $arr[] = $action->toArray();
        }

        return $arr;
    }

}
