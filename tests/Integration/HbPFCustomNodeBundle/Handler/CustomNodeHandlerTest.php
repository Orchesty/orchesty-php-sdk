<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Integration\HbPFCustomNodeBundle\Handler;

use Exception;
use Hanaboso\PipesPhpSdk\HbPFCustomNodeBundle\Handler\CustomNodeHandler;
use Hanaboso\PipesPhpSdk\Utils\ProcessDtoFactory;
use Hanaboso\Utils\String\Json;
use PipesPhpSdkTests\KernelTestCaseAbstract;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CustomNodeHandlerTest
 *
 * @package PipesPhpSdkTests\Integration\HbPFCustomNodeBundle\Handler
 */
final class CustomNodeHandlerTest extends KernelTestCaseAbstract
{

    /**
     * @var CustomNodeHandler
     */
    private CustomNodeHandler $handler;

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFCustomNodeBundle\Handler\CustomNodeHandler
     * @covers \Hanaboso\PipesPhpSdk\HbPFCustomNodeBundle\Handler\CustomNodeHandler::processAction
     *
     * @throws Exception
     */
    public function testProcess(): void
    {
        $dto = $this->handler->processAction(
            'null',
            new Request(content: Json::encode([ProcessDtoFactory::BODY => '', ProcessDtoFactory::HEADERS => []])),
        );

        self::assertEquals('', $dto->getData());
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFCustomNodeBundle\Handler\CustomNodeHandler::processTest
     *
     * @throws Exception
     */
    public function testProcessTest(): void
    {
        $this->handler->processTest('null');
        self::assertFake();
    }

    /**
     * @covers \Hanaboso\PipesPhpSdk\HbPFCustomNodeBundle\Handler\CustomNodeHandler::getCustomNodes
     */
    public function testGetCustomNodes(): void
    {
        self::assertEquals(1, count($this->handler->getCustomNodes()));
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = self::getContainer()->get('hbpf.handler.custom_node');
    }

}
