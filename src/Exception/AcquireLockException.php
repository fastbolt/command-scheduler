<?php

namespace Fastbolt\CommandScheduler\Exception;

use Symfony\Component\Console\Exception\RuntimeException;

class AcquireLockException extends RuntimeException
{
    public function __construct(
        private readonly string $lockName,
    ) {
        parent::__construct(
            sprintf(
                'Unable to acquire lock "%s"',
                $this->lockName
            )
        );
    }

    /**
     * @return string
     */
    public function getLockName(): string
    {
        return $this->lockName;
    }
}
