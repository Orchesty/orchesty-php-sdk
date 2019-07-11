<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\HbPFMapperBundle\Loader;

use Hanaboso\CommonsBundle\Utils\NodeServiceLoaderUtil;
use Hanaboso\PipesPhpSdk\HbPFMapperBundle\Exception\MapperException;
use Hanaboso\PipesPhpSdk\Mapper\MapperInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MapperLoader
 *
 * @package Hanaboso\PipesPhpSdk\HbPFMapperBundle\Loader
 */
class MapperLoader
{

    public const PREFIX = 'hbpf.mapper';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * MapperLoader constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $id
     *
     * @return MapperInterface
     * @throws MapperException
     */
    public function loadMapper(string $id): MapperInterface
    {
        $name = sprintf('%s.%s', MapperLoader::PREFIX, $id);
        if ($this->container->has($name)) {
            $mapper = $this->container->get($name);
            if ($mapper instanceof MapperInterface) {
                return $mapper;
            }
        }

        throw new MapperException(
            sprintf('Mapper \'%s\' not exist', $name),
            MapperException::MAPPER_NOT_EXIST
        );
    }

    /**
     * @param array $exclude
     *
     * @return array
     */
    public function getAllMappers(array $exclude = []): array
    {
        $dirs = $this->container->getParameter('node_services_dirs');

        return NodeServiceLoaderUtil::getServices($dirs, self::PREFIX, $exclude);
    }

}