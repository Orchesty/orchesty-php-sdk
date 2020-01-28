<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\HbPFCustomNodeBundle\Loader;

use Hanaboso\CommonsBundle\Utils\NodeServiceLoader;
use Hanaboso\PipesPhpSdk\CustomNode\CustomNodeInterface;
use Hanaboso\PipesPhpSdk\HbPFCustomNodeBundle\Exception\CustomNodeException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CustomNodeLoader
 *
 * @package Hanaboso\PipesPhpSdk\HbPFCustomNodeBundle\Loader
 */
final class CustomNodeLoader
{

    public const PREFIX = 'hbpf.custom_node';

    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * CustomNodeLoader constructor.
     *
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $serviceName
     *
     * @return CustomNodeInterface
     * @throws CustomNodeException
     */
    public function get(string $serviceName): CustomNodeInterface
    {
        $name = sprintf('%s.%s', self::PREFIX, $serviceName);
        if ($this->container->has($name)) {
            /** @var CustomNodeInterface $node */
            $node = $this->container->get($name);

            return $node;
        }

        throw new CustomNodeException(
            sprintf('Node [%s] not found.', $serviceName),
            CustomNodeException::CUSTOM_NODE_SERVICE_NOT_FOUND
        );
    }

    /**
     * @param mixed[] $exclude
     *
     * @return mixed[]
     */
    public function getAllCustomNodes(array $exclude = []): array
    {
        $dirs = $this->container->getParameter('node_services_dirs');

        return NodeServiceLoader::getServices($dirs, self::PREFIX, $exclude);
    }

}
