<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Application\Manager;

use Doctrine\Common\Annotations\CachedReader;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use Hanaboso\CommonsBundle\Transport\Curl\CurlManager;
use Hanaboso\PipesPhpSdk\Application\Base\ApplicationInterface;
use Hanaboso\PipesPhpSdk\Application\Document\ApplicationInstall;
use Hanaboso\PipesPhpSdk\Application\Exception\ApplicationInstallException;
use Hanaboso\PipesPhpSdk\Application\Loader\ApplicationLoader;
use Hanaboso\PipesPhpSdk\Application\Repository\ApplicationInstallRepository;
use Hanaboso\PipesPhpSdk\Application\Utils\SynchronousAction;
use Hanaboso\PipesPhpSdk\Authorization\Base\Basic\BasicApplicationInterface;
use Hanaboso\PipesPhpSdk\Authorization\Base\OAuth1\OAuth1ApplicationInterface;
use Hanaboso\PipesPhpSdk\Authorization\Base\OAuth2\OAuth2ApplicationInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApplicationManager
 *
 * @package Hanaboso\PipesPhpSdk\Application\Manager
 */
class ApplicationManager
{

    /**
     * @var ApplicationLoader
     */
    protected ApplicationLoader $loader;

    /**
     * @var DocumentManager
     */
    protected DocumentManager $dm;

    /**
     * @var ObjectRepository<ApplicationInstall>&ApplicationInstallRepository
     */
    protected $repository;

    /**
     * @var CachedReader
     */
    private CachedReader $reader;

    /**
     * ApplicationManager constructor.
     *
     * @param DocumentManager   $dm
     * @param ApplicationLoader $loader
     * @param CachedReader      $reader
     */
    public function __construct(DocumentManager $dm, ApplicationLoader $loader, CachedReader $reader)
    {
        $this->loader     = $loader;
        $this->dm         = $dm;
        $this->repository = $this->dm->getRepository(ApplicationInstall::class);
        $this->reader     = $reader;
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
     * @throws ReflectionException
     */
    public function getSynchronousActions(string $key): array
    {
        $actions    = [];
        $reflection = new ReflectionClass($this->getApplication($key));
        $methods    = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if ($this->reader->getMethodAnnotation($method, SynchronousAction::class)) {
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
    public function runSynchronousAction(string $key, string $method, Request $request)
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
            ApplicationInstallException::METHOD_NOT_FOUND
        );
    }

    /**
     * @param string  $key
     * @param string  $user
     * @param mixed[] $data
     *
     * @return ApplicationInstall
     * @throws Exception
     */
    public function saveApplicationSettings(string $key, string $user, array $data): ApplicationInstall
    {
        $application = $this->loader->getApplication($key)
            ->setApplicationSettings(
                $this->repository->findUserApp($key, $user),
                $data
            );
        $this->dm->flush();

        return $application;
    }

    /**
     * @param string $key
     * @param string $user
     * @param string $password
     *
     * @return ApplicationInstall
     * @throws Exception
     */
    public function saveApplicationPassword(string $key, string $user, string $password): ApplicationInstall
    {
        /** @var BasicApplicationInterface $application */
        $application = $this->loader->getApplication($key);
        $application = $application->setApplicationPassword(
            $this->repository->findUserApp($key, $user),
            $password
        );
        $this->dm->flush();

        return $application;
    }

    /**
     * @param string $key
     * @param string $user
     * @param string $redirectUrl
     *
     * @return string
     * @throws ApplicationInstallException
     * @throws MongoDBException
     */
    public function authorizeApplication(string $key, string $user, string $redirectUrl): string
    {
        $applicationInstall = $this->repository->findUserApp($key, $user);

        /** @var OAuth1ApplicationInterface|OAuth2ApplicationInterface $application */
        $application = $this->loader->getApplication($key);
        $application->setFrontendRedirectUrl($applicationInstall, $redirectUrl);
        $this->dm->flush();

        return $application->authorize($applicationInstall);
    }

    /**
     * @param string  $key
     * @param string  $user
     * @param mixed[] $token
     *
     * @return mixed[]
     * @throws ApplicationInstallException
     * @throws MongoDBException
     */
    public function saveAuthorizationToken(string $key, string $user, array $token): array
    {
        $applicationInstall = $this->repository->findUserApp($key, $user);

        /** @var OAuth1ApplicationInterface|OAuth2ApplicationInterface $application */
        $application = $this->loader->getApplication($key);
        $application->setAuthorizationToken($applicationInstall, $token);
        $this->dm->flush();

        return [ApplicationInterface::REDIRECT_URL => $application->getFrontendRedirectUrl($applicationInstall)];
    }

}
