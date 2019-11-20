<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\LongRunningNode\Model;

use Doctrine\ODM\MongoDB\DocumentManager;
use Hanaboso\CommonsBundle\Utils\Json;
use Hanaboso\PipesPhpSdk\LongRunningNode\Document\LongRunningNodeData;

/**
 * Class LongRunningNodeManager
 *
 * @package Hanaboso\PipesPhpSdk\LongRunningNode\Model
 */
class LongRunningNodeManager
{

    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * LongRunningNodeManager constructor.
     *
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * @param LongRunningNodeData $doc
     *
     * @return LongRunningNodeData
     */
    public function saveDocument(LongRunningNodeData $doc): LongRunningNodeData
    {
        $this->dm->persist($doc);
        $this->dm->flush();

        return $doc;
    }

    /**
     * @param string      $topologyId
     * @param string      $nodeId
     * @param null|string $token
     *
     * @return LongRunningNodeData|null
     */
    public function getDocument(string $topologyId, string $nodeId, ?string $token = NULL): ?LongRunningNodeData
    {
        $filter = [
            'topologyId' => $topologyId,
            'nodeId'     => $nodeId,
        ];
        if ($token) {
            $filter['processId'] = $token;
        }

        return $this->dm->getRepository(LongRunningNodeData::class)->findOneBy($filter);
    }

    /**
     * @param LongRunningNodeData $node
     * @param array               $data
     *
     * @return LongRunningNodeData
     */
    public function update(LongRunningNodeData $node, array $data): LongRunningNodeData
    {
        if (isset($data['data'])) {
            $node->setData(Json::encode($data['data']));
        }

        $this->dm->flush();

        return $node;
    }

    /**
     * @param LongRunningNodeData $doc
     */
    public function delete(LongRunningNodeData $doc): void
    {
        $this->dm->remove($doc);
        $this->dm->flush();
    }

}
