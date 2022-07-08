<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\CustomNode\Impl;

use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PipesPhpSdk\CustomNode\CommonNodeAbstract;
use Hanaboso\Utils\System\PipesHeaders;

/**
 * Class NullCustomNode
 *
 * @package Hanaboso\PipesPhpSdk\CustomNode\Impl
 */
final class NullCustomNode extends CommonNodeAbstract
{

    /**
     * @param ProcessDto $dto
     *
     * @return ProcessDto
     */
    public function processAction(ProcessDto $dto): ProcessDto
    {
        $dto->addHeader(PipesHeaders::RESULT_MESSAGE, 'Null worker resending data.');

        return $dto;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'nullCustomNode';
    }

}
