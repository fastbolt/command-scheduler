<?php

namespace Fastbolt\CommandScheduler\Model;

use InvalidArgumentException;
use Override;

/**
 * @api
 */
class CommandStatus implements CommandStatusInterface
{
    private int $numErrors = 0;

    private int $numSuccess = 0;

    private float $progress = 0.0;

    private array $additional = [];

    /**
     * @param int                     $numErrors
     * @param int                     $numSuccess
     * @param float                   $progress
     * @param array<array-key, mixed> $additional
     *
     * @return self
     */
    public static function create(int $numErrors, int $numSuccess, float $progress = 0.0, array $additional = []): self
    {
        $instance = new self();
        $instance->setNumErrors($numErrors);
        $instance->setNumSuccess($numSuccess);
        $instance->setProgress($progress);
        $instance->setAdditional($additional);

        return $instance;
    }

    /**
     * @return int
     */
    #[Override]
    public function getNumErrors(): int
    {
        return $this->numErrors;
    }

    /**
     * @param int $numErrors
     */
    #[Override]
    public function setNumErrors(int $numErrors): void
    {
        $this->numErrors = $numErrors;
    }

    /**
     * @return int
     */
    #[Override]
    public function getNumSuccess(): int
    {
        return $this->numSuccess;
    }

    /**
     * @param int $numSuccess
     */
    #[Override]
    public function setNumSuccess(int $numSuccess): void
    {
        $this->numSuccess = $numSuccess;
    }

    /**
     * @return float
     */
    #[Override]
    public function getProgress(): float
    {
        return $this->progress;
    }

    /**
     * @param float $progress
     */
    #[Override]
    public function setProgress(float $progress): void
    {
        if ($progress > 100 || $progress < 0) {
            throw new InvalidArgumentException('Progress must be between 0 and 100');
        }

        $this->progress = $progress;
    }

    /**
     * @param int $increment
     *
     * @return void
     */
    #[Override]
    public function increaseSuccess(int $increment = 1): void
    {
        $this->numSuccess += $increment;
    }

    /**
     * @param int $increment
     *
     * @return void
     */
    #[Override]
    public function increaseError(int $increment = 1): void
    {
        $this->numErrors += $increment;
    }

    /**
     * @param float $increment
     *
     * @return void
     */
    #[Override]
    public function increaseProgress(float $increment = 1): void
    {
        $this->progress += $increment;
    }

    /**
     * @return array<array-key, mixed>
     */
    #[Override]
    public function getAdditional(): array
    {
        return $this->additional;
    }

    /**
     * @param array<array-key, mixed> $additional
     */
    #[Override]
    public function setAdditional(array $additional): void
    {
        $this->additional = $additional;
    }
}
