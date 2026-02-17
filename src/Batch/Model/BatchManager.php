<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Batch\Model;

use Hanaboso\CommonsBundle\Process\BatchProcessDto;
use Hanaboso\PipesPhpSdk\Batch\BatchAbstract;
use Hanaboso\PipesPhpSdk\CustomNode\Exception\CustomNodeException;
use Hanaboso\PipesPhpSdk\Utils\ProcessDtoFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BatchManager
 *
 * @package Hanaboso\PipesPhpSdk\Batch\Model
 */
final class BatchManager
{

    /**
     * @param BatchAbstract $conn
     * @param Request       $request
     *
     * @return BatchProcessDto
     */
    public function processAction(BatchAbstract $conn, Request $request): BatchProcessDto
    {
        $dto = ProcessDtoFactory::createBatchFromRequest($request);

        try {
            $dto->setCurrentApp($conn->getApplicationKey());
        } catch (CustomNodeException) {
        }

        return $conn->processAction($dto);
    }

}
