<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Authorization\Manager;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Exception;
use Hanaboso\PipesPhpSdk\Authorization\Base\ApplicationInterface;
use Hanaboso\PipesPhpSdk\Authorization\Base\Basic\BasicApplicationInterface;
use Hanaboso\PipesPhpSdk\Authorization\Base\OAuth1\OAuth1ApplicationInterface;
use Hanaboso\PipesPhpSdk\Authorization\Base\OAuth2\OAuth2ApplicationInterface;
use Hanaboso\PipesPhpSdk\Authorization\Document\ApplicationInstall;
use Hanaboso\PipesPhpSdk\Authorization\Exception\ApplicationInstallException;
use Hanaboso\PipesPhpSdk\Authorization\Loader\ApplicationLoader;
use Hanaboso\PipesPhpSdk\Authorization\Repository\ApplicationInstallRepository;

/**
 * Class ApplicationManager
 *
 * @package Hanaboso\HbPFApplication\Model
 */
class ApplicationManager
{

    /**
     * @var ApplicationLoader
     */
    private $loader;

    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @var ObjectRepository|ApplicationInstallRepository
     */
    private $repository;

    /**
     * ApplicationManager constructor.
     *
     * @param DocumentManager   $dm
     * @param ApplicationLoader $loader
     */
    public function __construct(DocumentManager $dm, ApplicationLoader $loader)
    {
        $this->loader     = $loader;
        $this->dm         = $dm;
        $this->repository = $this->dm->getRepository(ApplicationInstall::class);
    }

    /**
     * @return array
     */
    public function getApplications(): array
    {
        return $this->loader->getApplications();
    }

    /**
     * @param string $key
     * @param string $user
     * @param array  $data
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
        $this->dm->flush($application);

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
        $this->dm->flush($application);

        return $application;
    }

    /**
     * @param string $key
     * @param string $user
     * @param string $redirectUrl
     *
     * @throws ApplicationInstallException
     */
    public function authorizeApplication(string $key, string $user, string $redirectUrl): void
    {
        $applicationInstall = $this->repository->findUserApp($key, $user);

        /** @var OAuth1ApplicationInterface|OAuth2ApplicationInterface $application */
        $application = $this->loader->getApplication($key);
        $application->setFrontendRedirectUrl($applicationInstall, $redirectUrl);
        $this->dm->flush();

        $application->authorize($applicationInstall);
    }

    /**
     * @param string $key
     * @param string $user
     * @param array  $token
     *
     * @return array
     * @throws ApplicationInstallException
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