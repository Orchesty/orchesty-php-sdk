<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Unit\Utils;

use Hanaboso\PipesPhpSdk\Utils\ProcessHeaderTrait;

/**
 * Class NullProcessHeader
 *
 * @package PipesPhpSdkTests\Unit\Utils
 */
final class NullProcessHeader
{

    use ProcessHeaderTrait;

    /**
     * @return string
     */
    public function getName(): string
    {
        return '1';
    }

}
