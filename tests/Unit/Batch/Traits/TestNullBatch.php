<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Unit\Batch\Traits;

use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PipesPhpSdk\Batch\BatchAbstract;
use Hanaboso\PipesPhpSdk\Batch\Traits\ProcessExceptionTrait;

/**
 * Class TestNullBatch
 *
 * @package PipesPhpSdkTests\Unit\Batch\Traits
 */
final class TestNullBatch extends BatchAbstract
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
