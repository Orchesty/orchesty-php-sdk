<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\HbPFCustomNodeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Hanaboso\PipesPhpSdk\HbPFCustomNodeBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{

    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('hbpf');

        $rootNode->children()
            ->arrayNode('custom_node');

        return $treeBuilder;
    }

}
