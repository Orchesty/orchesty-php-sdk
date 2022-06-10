<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Unit\Batch;

use Exception;
use Hanaboso\CommonsBundle\Process\ProcessDto;
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
    private TestNullBatch $nullConnector;

    /**
     * @covers \Hanaboso\PipesPhpSdk\Batch\BatchAbstract::evaluateStatusCode
     *
     * @throws Exception
     */
    public function testEvaluateStatusCode(): void
    {
        $result = $this->nullConnector->evaluateStatusCode(200, new ProcessDto());
        self::assertTrue($result);

        $result = $this->nullConnector->evaluateStatusCode(400, new ProcessDto());
        self::assertFalse($result);
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Batch\BatchAbstract::setApplication
     */
    public function testSetApplication(): void
    {
        $this->nullConnector->setApplication(new TestNullApplication());

        self::assertEquals('null-key', $this->nullConnector->getApplicationKey());
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Batch\BatchAbstract::getApplicationKey
     */
    public function testGetApplicationKey(): void
    {
        self::assertNull($this->nullConnector->getApplicationKey());
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Batch\BatchAbstract::getApplication

     * @throws Exception
     */
    public function testGetApplicationException(): void
    {
        self::expectException(BatchException::class);
        self::expectExceptionCode(BatchException::MISSING_APPLICATION);
        $this->nullConnector->getApplication();
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Batch\BatchAbstract::getApplication

     * @throws Exception
     */
    public function testGetApplication(): void
    {
        $this->nullConnector->setApplication(new TestNullApplication());
        self::assertNotEmpty($this->nullConnector->getApplication());
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Batch\BatchAbstract::setJsonContent
     * @covers \Hanaboso\PipesPhpSdk\Batch\BatchAbstract::getJsonContent
     *
     * @throws Exception
     */
    public function testJsonContent(): void
    {
        $dto = new ProcessDto();
        $this->invokeMethod(
            $this->nullConnector,
            'setJsonContent',
            [$dto, ['data' => 'something']],
        );

        $result = $this->invokeMethod(
            $this->nullConnector,
            'getJsonContent',
            [$dto],
        );
        self::assertEquals(['data' => 'something'], $result);
    }

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->nullConnector = new TestNullBatch();
    }

}
