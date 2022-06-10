<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Unit\HbPFBatchBundle\Loader;

use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PipesPhpSdk\Batch\BatchAbstract;

/**
 * Class NullBatch
 *
 * @package PipesPhpSdkTests\Unit\HbPFBatchBundle\Loader
 */
final class NullBatch extends BatchAbstract
{

    /**
     * @return string
     */
    public function getId(): string
    {
        return '0';
    }

    /**
     * @param ProcessDto $dto
     *
     * @return ProcessDto
     */
    public function processEvent(ProcessDto $dto): ProcessDto
    {
        return $dto;
    }

    /**
     * @param ProcessDto $dto
     *
     * @return ProcessDto
     */
    public function processAction(ProcessDto $dto): ProcessDto
    {
        return $dto;
    }

}
