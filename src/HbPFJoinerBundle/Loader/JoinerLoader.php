<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\HbPFJoinerBundle\Loader;

use Hanaboso\CommonsBundle\Utils\NodeServiceLoaderUtil;
use Hanaboso\PipesPhpSdk\HbPFJoinerBundle\Exception\JoinerException;
use Hanaboso\PipesPhpSdk\Joiner\JoinerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class JoinerLoader
 *
 * @package Hanaboso\PipesPhpSdk\HbPFJoinerBundle\Loader
 */
final class JoinerLoader
{

    public const PREFIX = 'hbpf.joiner';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * JoinerLoader constructor.
     *
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $joiner
     *
     * @return JoinerInterface
     * @throws JoinerException
     */
    public function get(string $joiner): JoinerInterface
    {
        $name = sprintf('%s.%s', self::PREFIX, $joiner);
        if ($this->container->has($name)) {
            /** @var JoinerInterface $joiner */
            $joiner = $this->container->get($name);

            return $joiner;
        }

        throw new JoinerException(
            sprintf('Joiner [%s] not found.', $joiner),
            JoinerException::JOINER_SERVICE_NOT_FOUND
        );
    }

    /**
     * @param array $exclude
     *
     * @return array
     */
    public function getAllJoiners(array $exclude = []): array
    {
        $dirs = $this->container->getParameter('node_services_dirs');

        return NodeServiceLoaderUtil::getServices($dirs, self::PREFIX, $exclude);
    }

}
