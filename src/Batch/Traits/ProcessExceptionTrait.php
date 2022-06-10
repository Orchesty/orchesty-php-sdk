<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Batch\Traits;

use Hanaboso\CommonsBundle\Exception\OnRepeatException;
use Hanaboso\CommonsBundle\Process\ProcessDto;
use Hanaboso\PipesPhpSdk\Batch\Exception\BatchException;
use Throwable;

/**
 * Trait ProcessExceptionTrait
 *
 * @package Hanaboso\PipesPhpSdk\Batch\Traits
 */
trait ProcessExceptionTrait
{

    /**
     * @return string
     */
    abstract public function getId(): string;

    /**
     * @param string $message
     * @param string ...$arguments
     *
     * @return BatchException
     */
    protected function createException(string $message, string ...$arguments): BatchException
    {
        $message = sprintf("Batch '%s': %s", $this->getId(), $message);

        if ($arguments) {
            $message = sprintf($message, ...$arguments);
        }

        return new BatchException($message, BatchException::BATCH_FAILED_TO_PROCESS);
    }

    /**
     * @param string $key
     *
     * @return BatchException
     */
    protected function createMissingContentException(string $key): BatchException
    {
        return $this->createException("Content '%s' does not exist!", $key);
    }

    /**
     * @param string $key
     *
     * @return BatchException
     */
    protected function createMissingHeaderException(string $key): BatchException
    {
        return $this->createException("Header '%s' does not exist!", $key);
    }

    /**
     * @param string $key
     *
     * @return BatchException
     */
    protected function createMissingApplicationInstallException(string $key): BatchException
    {
        return $this->createException("ApplicationInstall with key '%s' does not exist!", $key);
    }

    /**
     * @param ProcessDto $dto
     * @param Throwable  $throwable
     * @param int        $interval
     * @param int        $maxHops
     *
     * @return OnRepeatException
     */
    protected function createRepeatException(
        ProcessDto $dto,
        Throwable $throwable,
        int $interval = 60_000,
        int $maxHops = 10,
    ): OnRepeatException
    {
        $message = sprintf("Batch '%s': %s: %s", $this->getId(), $throwable::class, $throwable->getMessage());

        return (new OnRepeatException($dto, $message, $throwable->getCode(), $throwable))
            ->setInterval($interval)
            ->setMaxHops($maxHops);
    }

}
