<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Batch;

use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PipesPhpSdk\Application\Base\ApplicationInterface;
use Hanaboso\PipesPhpSdk\Connector\Exception\ConnectorException;

/**
 * Interface BatchInterface
 *
 * @package Hanaboso\PipesPhpSdk\Batch
 */
interface BatchInterface
{

    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @param ProcessDto $dto
     *
     * @return ProcessDto
     * @throws ConnectorException
     */
    public function processAction(ProcessDto $dto): ProcessDto;

    /**
     * @param ApplicationInterface $application
     *
     * @return BatchInterface
     */
    public function setApplication(ApplicationInterface $application): BatchInterface;

    /**
     * @return string|null
     */
    public function getApplicationKey(): ?string;

}
