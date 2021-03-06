<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Unit\Connector;

use Exception;
use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\PrivateTrait;
use Hanaboso\PipesPhpSdk\Connector\Exception\ConnectorException;
use PipesPhpSdkTests\Integration\Application\TestNullApplication;
use PipesPhpSdkTests\KernelTestCaseAbstract;
use PipesPhpSdkTests\Unit\Connector\Traits\TestNullConnector;

/**
 * Class ConnectorAbstractTest
 *
 * @package PipesPhpSdkTests\Unit\Connector
 */
final class ConnectorAbstractTest extends KernelTestCaseAbstract
{

    use PrivateTrait;

    /**
     * @var TestNullConnector
     */
    private TestNullConnector $nullConnector;

    /**
     * @covers \Hanaboso\PipesPhpSdk\Connector\ConnectorAbstract::evaluateStatusCode
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
     * @covers \Hanaboso\PipesPhpSdk\Connector\ConnectorAbstract::setApplication
     */
    public function testSetApplication(): void
    {
        $this->nullConnector->setApplication(new TestNullApplication());

        self::assertEquals('null-key', $this->nullConnector->getApplicationKey());
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Connector\ConnectorAbstract::getApplicationKey
     */
    public function testGetApplicationKey(): void
    {
        self::assertNull($this->nullConnector->getApplicationKey());
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Connector\ConnectorAbstract::getApplication

     * @throws Exception
     */
    public function testGetApplicationException(): void
    {
        self::expectException(ConnectorException::class);
        self::expectExceptionCode(ConnectorException::MISSING_APPLICATION);
        $this->nullConnector->getApplication();
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Connector\ConnectorAbstract::getApplication

     * @throws Exception
     */
    public function testGetApplication(): void
    {
        $this->nullConnector->setApplication(new TestNullApplication());
        self::assertNotEmpty($this->nullConnector->getApplication());
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\Connector\ConnectorAbstract::setJsonContent
     * @covers \Hanaboso\PipesPhpSdk\Connector\ConnectorAbstract::getJsonContent
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

        $this->nullConnector = new TestNullConnector();
    }

}
