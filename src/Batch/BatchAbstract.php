<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Batch;

use Hanaboso\CommonsBundle\Process\BatchProcessDto;
use Hanaboso\CommonsBundle\Process\ProcessDtoAbstract;
use Hanaboso\PipesPhpSdk\Application\Base\ApplicationInterface;
use Hanaboso\PipesPhpSdk\Batch\Exception\BatchException;
use Hanaboso\Utils\Exception\PipesFrameworkException;

/**
 * Class BatchAbstract
 *
 * @package Hanaboso\PipesPhpSdk\Batch
 */
abstract class BatchAbstract implements BatchInterface
{

    /**
     * @var ApplicationInterface|null
     */
    protected ?ApplicationInterface $application = NULL;

    /**
     * @var mixed[]
     */
    protected array $okStatuses = [
        200,
        201,
    ];

    /**
     * @var mixed[]
     */
    protected array $badStatuses = [
        409,
        400,
    ];

    /**
     * @param int             $statusCode
     * @param BatchProcessDto $dto
     * @param string|null     $message
     *
     * @return bool
     * @throws PipesFrameworkException
     */
    public function evaluateStatusCode(int $statusCode, BatchProcessDto $dto, ?string $message = NULL): bool
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

        $dto->setStopProcess(ProcessDtoAbstract::STOP_AND_FAILED, $message);

        return FALSE;
    }

    /**
     * @param ApplicationInterface $application
     *
     * @return BatchInterface
     */
    public function setApplication(ApplicationInterface $application): BatchInterface
    {
        $this->application = $application;

        return $this;
    }

    /**
     * @return ApplicationInterface
     * @throws BatchException
     */
    public function getApplication(): ApplicationInterface
    {
        if ($this->application) {
            return $this->application;
        }

        throw new BatchException('Application has not set.', BatchException::MISSING_APPLICATION);
    }

    /**
     * @return string|null
     */
    public function getApplicationKey(): ?string
    {
        if ($this->application) {
            return $this->application->getName();
        }

        return NULL;
    }

}
