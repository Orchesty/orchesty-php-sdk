<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Unit\Batch;

use Exception;
use Hanaboso\CommonsBundle\Process\BatchProcessDto;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\PrivateTrait;
use Hanaboso\PipesPhpSdk\Batch\Exception\BatchException;
use PipesPhpSdkTests\Integration\Application\TestNullApplication;
use PipesPhpSdkTests\KernelTestCaseAbstract;
use PipesPhpSdkTests\Unit\Batch\Traits\TestNullBatch;

/**
 * Class BatchAbstractTest
 *
 * @package PipesPhpSdkTests\Unit\Batch
 */
final class BatchAbstractTest extends KernelTestCaseAbstract
{

    use PrivateTrait;

    /**
     * @var TestNullBatch
     */
    private TestNullBatch $nullBatchConnector;

    /**
     * @covers \Hanaboso\PipesPhpSdk\Batch\BatchAbstract::evaluateStatusCode
     *
     * @throws Exception
     */
    public function testEvaluateStatusCode(): void
    {
        $result = $this->nullBatchConnector->evaluateStatusCode(200, new BatchProcessDto());
        self::assertTrue($result);

        $result = $this->nullBatchConnector->evaluateStatusCode(400, new BatchProcessDto());
        self::assertFalse($result);
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Batch\BatchAbstract::setApplication
     */
    public function testSetApplication(): void
    {
        $this->nullBatchConnector->setApplication(new TestNullApplication());

        self::assertEquals('null-key', $this->nullBatchConnector->getApplicationKey());
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Batch\BatchAbstract::getApplicationKey
     */
    public function testGetApplicationKey(): void
    {
        self::assertNull($this->nullBatchConnector->getApplicationKey());
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Batch\BatchAbstract::getApplication

     * @throws Exception
     */
    public function testGetApplicationException(): void
    {
        self::expectException(BatchException::class);
        self::expectExceptionCode(BatchException::MISSING_APPLICATION);
        $this->nullBatchConnector->getApplication();
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Batch\BatchAbstract::getApplication

     * @throws Exception
     */
    public function testGetApplication(): void
    {
        $this->nullBatchConnector->setApplication(new TestNullApplication());
        self::assertNotEmpty($this->nullBatchConnector->getApplication());
    }

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->nullBatchConnector = new TestNullBatch();
    }

}
