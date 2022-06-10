<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Unit\CustomNode\Imp;

use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PipesPhpSdk\CustomNode\Impl\Sleep05CustomNode;
use PipesPhpSdkTests\KernelTestCaseAbstract;

/**
 * Class Sleep05CustomNodeTest
 *
 * @package PipesPhpSdkTests\Unit\CustomNode\Imp
 */
final class Sleep05CustomNodeTest extends KernelTestCaseAbstract
{

    /**
     * @covers \Hanaboso\PipesPhpSdk\CustomNode\Impl\Sleep05CustomNode::processAction
     */
    public function testProcess(): void
    {
        $start = microtime(TRUE);
        (new Sleep05CustomNode())->processAction(new ProcessDto());
        $time = microtime(TRUE) - $start;
        self::assertGreaterThan(0.5, $time);
    }

}
