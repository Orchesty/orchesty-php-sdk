<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\CustomNode;

use Doctrine\ODM\MongoDB\DocumentManager;
use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PipesPhpSdk\Application\Base\ApplicationInterface;
use Hanaboso\PipesPhpSdk\Application\Document\ApplicationInstall;
use Hanaboso\PipesPhpSdk\Application\Exception\ApplicationInstallException;
use Hanaboso\PipesPhpSdk\Application\Repository\ApplicationInstallRepository;
use Hanaboso\PipesPhpSdk\Connector\Exception\ConnectorException;
use LogicException;

/**
 * Class CommonNodeAbstract
 *
 * @package Hanaboso\PipesPhpSdk\CustomNode
 */
abstract class CommonNodeAbstract implements CommonNodeInterface
{

    /**
     * @var ApplicationInterface|null
     */
    protected ?ApplicationInterface $application = NULL;

    /**
     * @var DocumentManager|null
     */
    protected ?DocumentManager $db = NULL;

    /**
     * @param ProcessDto $dto
     *
     * @return ProcessDto
     */
    abstract function processAction(ProcessDto $dto): ProcessDto;

    /**
     * @return string
     */
    abstract function getName(): string;

    /**
     * @param ApplicationInterface $application
     *
     * @return CommonNodeInterface
     */
    public function setApplication(ApplicationInterface $application): CommonNodeInterface
    {
        $this->application = $application;

        return $this;
    }

    /**
     * @return ApplicationInterface
     * @throws ConnectorException
     */
    public function getApplication(): ApplicationInterface
    {
        if ($this->application) {
            return $this->application;
        }

        throw new ConnectorException('Application has not set.', ConnectorException::MISSING_APPLICATION);
    }

    /**
     * @return string
     */
    public function getApplicationKey(): string
    {
        if ($this->application) {
            return $this->application->getName();
        }

        throw new LogicException('Application has not set.');
    }

    /**
     * @return DocumentManager
     */
    public function getDb(): DocumentManager
    {
        if ($this->db) {
            return $this->db;
        }

        throw new LogicException('MongoDbClient is not set.');
    }

    /**
     * @param DocumentManager|null $db
     *
     * @return CommonNodeAbstract
     */
    public function setDb(?DocumentManager $db): CommonNodeAbstract
    {
        $this->db = $db;

        return $this;
    }

    /**
     * @param string|null $user
     *
     * @return ApplicationInstall
     * @throws ApplicationInstallException
     */
    protected function getApplicationInstall(?string $user): ApplicationInstall {
        /** @var ApplicationInstallRepository $repo */
        $repo = $this->getDb()->getRepository(ApplicationInstall::class);
        if ($user) {
            return $repo->findUserApp($this->getApplicationKey(), $user);
        }

        return $repo->findOneByName($this->getApplicationKey());
    }

    /**
     * @param ProcessDto $dto
     *
     * @return ApplicationInstall
     * @throws ApplicationInstallException
     */
    protected function getApplicationInstallFromProcess(ProcessDto $dto): ApplicationInstall {
        $user = $dto->getUser();
        if (!$user) {
            throw new LogicException('User not defined');
        }

        return $this->getApplicationInstall($user);
    }

}
