<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Unit\Batch\Exception;

use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PipesPhpSdk\Batch\Exception\BatchException;
use PipesPhpSdkTests\KernelTestCaseAbstract;

/**
 * Class BatchExceptionTest
 *
 * @package PipesPhpSdkTests\Unit\Batch\Exception
 *
 * @covers  \Hanaboso\PipesPhpSdk\Batch\Exception\BatchException
 */
final class BatchExceptionTest extends KernelTestCaseAbstract
{

    /**
     * @covers \Hanaboso\PipesPhpSdk\Batch\Exception\BatchException::getProcessDto
     * @covers \Hanaboso\PipesPhpSdk\Batch\Exception\BatchException::setProcessDto
     */
    public function testException(): void
    {
        $dto = new ProcessDto();

        self::assertEquals($dto, (new BatchException())->setProcessDto($dto)->getProcessDto());
    }

}
