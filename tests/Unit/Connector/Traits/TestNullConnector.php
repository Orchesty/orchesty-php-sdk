<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Unit\Connector\Traits;

use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PipesPhpSdk\Connector\ConnectorAbstract;
use Hanaboso\PipesPhpSdk\Connector\Traits\ProcessExceptionTrait;

/**
 * Class TestNullConnector
 *
 * @package PipesPhpSdkTests\Unit\Connector\Traits
 */
final class TestNullConnector extends ConnectorAbstract
{

    use ProcessExceptionTrait;

    /**
     * @return string
     */
    public function getId(): string
    {
        return 'null-test-trait';
    }

    /**
     * @param ProcessDto $dto
     *
     * @return ProcessDto
     */
    public function processAction(ProcessDto $dto): ProcessDto
    {
        $dto;

        return new ProcessDto();
    }

}
