<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\CustomNode;

use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PipesPhpSdk\Application\Base\ApplicationInterface;

/**
 * Interface CommonNodeInterface
 *
 * @package Hanaboso\PipesPhpSdk\CustomNode
 */
interface CommonNodeInterface
{

    /**
     * @param ProcessDto $dto
     *
     * @return ProcessDto
     */
    public function processAction(ProcessDto $dto): ProcessDto;

    /**
     * @param ApplicationInterface $application
     *
     * @return CommonNodeInterface
     */
    public function setApplication(ApplicationInterface $application): CommonNodeInterface;

    /**
     * @return string|null
     */
    public function getApplicationKey(): ?string;

}
