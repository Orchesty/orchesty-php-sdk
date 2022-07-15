<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Unit\Batch\Traits;

use Hanaboso\CommonsBundle\Process\BatchProcessDto;
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
    public function getName(): string
    {
        return 'null-test-trait';
    }

    /**
     * @param BatchProcessDto $dto
     *
     * @return BatchProcessDto
     */
    public function processAction(BatchProcessDto $dto): BatchProcessDto
    {
        $dto;

        return new BatchProcessDto();
    }

}
