<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Connector\Exception;

use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\Utils\Exception\PipesFrameworkExceptionAbstract;
use Throwable;

/**
 * Class ConnectorException
 *
 * @package Hanaboso\PipesPhpSdk\Connector\Exception
 */
final class ConnectorException extends PipesFrameworkExceptionAbstract
{

    public const CONNECTOR_SERVICE_NOT_FOUND = self::OFFSET + 1;
    public const CONNECTOR_FAILED_TO_PROCESS = self::OFFSET + 3;
    public const INVALID_SETTING             = self::OFFSET + 6;
    public const MISSING_APPLICATION         = self::OFFSET + 8;

    protected const OFFSET = 1_000;

    /**
     * ConnectorException constructor.
     *
     * @param string          $message
     * @param int             $code
     * @param Throwable|null  $previous
     * @param ProcessDto|null $processDto
     */
    public function __construct(
        $message = '',
        $code = 0,
        ?Throwable $previous = NULL,
        private ?ProcessDto $processDto = NULL,
    )
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return ProcessDto|null
     */
    public function getProcessDto(): ?ProcessDto
    {
        return $this->processDto;
    }

    /**
     * @param ProcessDto|null $processDto
     *
     * @return ConnectorException
     */
    public function setProcessDto(?ProcessDto $processDto): ConnectorException
    {
        $this->processDto = $processDto;

        return $this;
    }

}
