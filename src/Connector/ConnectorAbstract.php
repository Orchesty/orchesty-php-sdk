<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Connector;

use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\CommonsBundle\Transport\Curl\CurlManager;
use Hanaboso\PipesPhpSdk\CustomNode\CommonNodeAbstract;
use Hanaboso\Utils\Exception\PipesFrameworkException;
use LogicException;

/**
 * Class ConnectorAbstract
 *
 * @package Hanaboso\PipesPhpSdk\Connector
 */
abstract class ConnectorAbstract extends CommonNodeAbstract implements ConnectorInterface
{

    protected ?CurlManager $sender;

    /**
     * @var mixed[]
     */
    protected array $okStatuses = [
        200,
        201,
    ];

    /**
     * @param int         $statusCode
     * @param ProcessDto  $dto
     * @param string|null $message
     *
     * @return bool
     * @throws PipesFrameworkException
     */
    public function evaluateStatusCode(int $statusCode, ProcessDto $dto, ?string $message = NULL): bool
    {
        if (in_array($statusCode, $this->okStatuses, TRUE)) {
            return TRUE;
        }

        if (!$message) {
            $message = sprintf(
                'Returned StatusCode [%d] is not in allowed statusCodes [%s]',
                $statusCode,
                implode(', ', $this->okStatuses),
            );
        }

        $dto->setStopProcess(ProcessDto::STOP_AND_FAILED, $message);

        return FALSE;
    }

    /**
     * @param CurlManager $sender
     *
     * @return $this
     */
    public function setSender(CurlManager $sender): self {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return CurlManager
     */
    protected function getSender(): CurlManager {
        if ($this->sender) {
            return $this->sender;
        }

        throw new LogicException('CurlManager has not set.');
    }

}
