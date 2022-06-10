<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Unit\Batch\Traits;

use Exception;
use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\PrivateTrait;
use Hanaboso\PipesPhpSdk\Batch\Exception\BatchException;
use PipesPhpSdkTests\KernelTestCaseAbstract;

/**
 * Class TraitTest
 *
 * @package PipesPhpSdkTests\Unit\Batch\Traits
 */
final class TraitTest extends KernelTestCaseAbstract
{

    use PrivateTrait;

    /**
     * @var TestNullBatch
     */
    private TestNullBatch $nullBatch;

    /**
     * @covers \Hanaboso\PipesPhpSdk\Batch\Traits\ProcessExceptionTrait::createException
     *
     * @throws Exception
     */
    public function testCreateException(): void
    {
        $exception = $this->invokeMethod(
            $this->nullBatch,
            'createException',
            ['%s. This is test exception', 'Uupps'],
        );

        self::assertEquals("Batch 'null-test-trait': Uupps. This is test exception", $exception->getMessage());
        self::assertEquals(BatchException::BATCH_FAILED_TO_PROCESS, $exception->getCode());
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Batch\Traits\ProcessExceptionTrait::createMissingContentException
     *
     * @throws Exception
     */
    public function testCreateMissingContentException(): void
    {
        $exception = $this->invokeMethod(
            $this->nullBatch,
            'createMissingContentException',
            ['Something'],
        );

        self::assertEquals(
            "Batch 'null-test-trait': Content 'Something' does not exist!",
            $exception->getMessage(),
        );
        self::assertEquals(BatchException::BATCH_FAILED_TO_PROCESS, $exception->getCode());
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Batch\Traits\ProcessExceptionTrait::createMissingHeaderException
     *
     * @throws Exception
     */
    public function testCreateMissingHeaderException(): void
    {
        $exception = $this->invokeMethod(
            $this->nullBatch,
            'createMissingHeaderException',
            ['Something'],
        );

        self::assertEquals(
            "Batch 'null-test-trait': Header 'Something' does not exist!",
            $exception->getMessage(),
        );
        self::assertEquals(BatchException::BATCH_FAILED_TO_PROCESS, $exception->getCode());
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Batch\Traits\ProcessExceptionTrait::createMissingApplicationInstallException
     *
     * @throws Exception
     */
    public function testCreateMissingApplicationInstallException(): void
    {
        $exception = $this->invokeMethod(
            $this->nullBatch,
            'createMissingApplicationInstallException',
            ['Something'],
        );

        self::assertEquals(
            "Batch 'null-test-trait': ApplicationInstall with key 'Something' does not exist!",
            $exception->getMessage(),
        );
        self::assertEquals(BatchException::BATCH_FAILED_TO_PROCESS, $exception->getCode());
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Batch\Traits\ProcessExceptionTrait::createRepeatException
     *
     * @throws Exception
     */
    public function testCreateRepeatException(): void
    {
        $exception = $this->invokeMethod(
            $this->nullBatch,
            'createRepeatException',
            [new ProcessDto(), new Exception('Upps. Something went wrong.'), 70_000, 15],
        );

        self::assertEquals(
            "Batch 'null-test-trait': Exception: Upps. Something went wrong.",
            $exception->getMessage(),
        );
        self::assertEquals(15, $exception->getMaxHops());
        self::assertEquals(70_000, $exception->getInterval());
    }

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->nullBatch = new TestNullBatch();
    }

}
