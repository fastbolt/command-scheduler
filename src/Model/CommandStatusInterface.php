<?php

namespace Fastbolt\CommandScheduler\Model;

interface CommandStatusInterface
{
    /**
     * @return int
     */
    public function getNumErrors(): int;

    /**
     * @param int $numErrors
     *
     * @return void
     */
    public function setNumErrors(int $numErrors): void;

    /**
     * @return int
     */
    public function getNumSuccess(): int;

    /**
     * @param int $numSuccess
     *
     * @return void
     */
    public function setNumSuccess(int $numSuccess): void;

    /**
     * @return float
     */
    public function getProgress(): float;

    /**
     * @param float $progress
     *
     * @return void
     */
    public function setProgress(float $progress): void;

    /**
     * @param int $increment
     *
     * @return void
     */
    public function increaseSuccess(int $increment): void;

    /**
     * @param int $increment
     *
     * @return void
     */
    public function increaseError(int $increment): void;

    /**
     * @param float $increment
     *
     * @return void
     */
    public function increaseProgress(float $increment): void;
}
