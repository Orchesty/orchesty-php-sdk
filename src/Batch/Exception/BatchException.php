<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Batch\Exception;

use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\Utils\Exception\PipesFrameworkExceptionAbstract;
use Throwable;

/**
 * Class BatchException
 *
 * @package Hanaboso\PipesPhpSdk\Batch\Exception
 */
final class BatchException extends PipesFrameworkExceptionAbstract
{

    public const BATCH_SERVICE_NOT_FOUND = self::OFFSET + 1;
    public const BATCH_FAILED_TO_PROCESS = self::OFFSET + 3;
    public const INVALID_SETTING         = self::OFFSET + 6;
    public const MISSING_APPLICATION     = self::OFFSET + 8;

    protected const OFFSET = 3_500;

    /**
     * BatchException constructor.
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
    ) {
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
     * @return BatchException
     */
    public function setProcessDto(?ProcessDto $processDto): BatchException
    {
        $this->processDto = $processDto;

        return $this;
    }

}
