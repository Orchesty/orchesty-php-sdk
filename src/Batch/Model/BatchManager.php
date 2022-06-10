<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Batch\Model;

use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PipesPhpSdk\Batch\BatchInterface;
use Hanaboso\PipesPhpSdk\Connector\Exception\ConnectorException;
use Hanaboso\PipesPhpSdk\Utils\ProcessDtoFactory;
use Hanaboso\Utils\System\PipesHeaders;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BatchManager
 *
 * @package Hanaboso\PipesPhpSdk\Batch\Model
 */
final class BatchManager
{

    /**
     * @param BatchInterface $conn
     * @param Request        $request
     *
     * @return ProcessDto
     * @throws ConnectorException
     */
    public function processAction(BatchInterface $conn, Request $request): ProcessDto
    {
        $dto = ProcessDtoFactory::createFromRequest($request);
        $key = $conn->getApplicationKey();
        if ($key) {
            $headers                                                     = $dto->getHeaders();
            $headers[PipesHeaders::createKey(PipesHeaders::APPLICATION)] = [$key];
            $dto->setHeaders($headers);
        }

        return $conn->processAction($dto);
    }

}
