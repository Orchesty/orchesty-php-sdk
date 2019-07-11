<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\CustomNode\Model\Batch;

use Hanaboso\PipesPhpSdk\HbPFCustomNodeBundle\Exception\CustomNodeException;
use Hanaboso\PipesPhpSdk\HbPFCustomNodeBundle\Loader\CustomNodeLoader;
use Hanaboso\PipesPhpSdk\RabbitMq\Impl\Batch\BatchActionAbstract;
use Hanaboso\PipesPhpSdk\RabbitMq\Impl\Batch\BatchInterface;
use InvalidArgumentException;

/**
 * Class BatchActionCallback
 *
 * @package Hanaboso\PipesPhpSdk\CustomNode\Model\Batch
 */
class BatchActionCallback extends BatchActionAbstract
{

    /**
     * @var CustomNodeLoader
     */
    private $customNodeLoader;

    /**
     * BatchActionCallback constructor.
     *
     * @param CustomNodeLoader $customNodeLoader
     */
    public function __construct(CustomNodeLoader $customNodeLoader)
    {
        parent::__construct();

        $this->customNodeLoader = $customNodeLoader;
    }

    /**
     * @param string $id
     *
     * @return BatchInterface
     * @throws CustomNodeException
     */
    public function getBatchService(string $id): BatchInterface
    {
        /** @var BatchInterface $node */
        $node = $this->customNodeLoader->get($id);

        if (!$node instanceof BatchInterface) {
            throw new InvalidArgumentException(sprintf('The custom node not implemented "%s".', BatchInterface::class));
        }

        return $node;
    }

}
