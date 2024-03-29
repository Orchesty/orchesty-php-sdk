<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Integration\HbPFConnectorBundle\Handler;

use Exception;
use Hanaboso\PipesPhpSdk\HbPFConnectorBundle\Handler\ConnectorHandler;
use Hanaboso\PipesPhpSdk\Utils\ProcessDtoFactory;
use Hanaboso\Utils\String\Json;
use PipesPhpSdkTests\KernelTestCaseAbstract;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ConnectorHandlerTest
 *
 * @package PipesPhpSdkTests\Integration\HbPFConnectorBundle\Handler
 */
final class ConnectorHandlerTest extends KernelTestCaseAbstract
{

    /**
     * @var ConnectorHandler
     */
    private ConnectorHandler $handler;

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFConnectorBundle\Handler\ConnectorHandler::processTest
     *
     * @throws Exception
     */
    public function testProcessTest(): void
    {
        $this->handler->processTest('null');

        self::assertFake();
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFConnectorBundle\Handler\ConnectorHandler::processAction
     *
     * @throws Exception
     */
    public function testProcessAction(): void
    {
        $dto = $this->handler->processAction(
            'null',
            new Request(content: Json::encode([ProcessDtoFactory::BODY => '', ProcessDtoFactory::HEADERS => []])),
        );

        self::assertEquals('', $dto->getData());
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = self::getContainer()->get('hbpf.handler.connector');
    }

}
