<?php

namespace Fastbolt\CommandScheduler\Exception;

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Lock\Exception\ExceptionInterface;

class UnknownLockException extends RuntimeException
{
    public function __construct(
        private readonly string $lockName,
        private readonly ?ExceptionInterface $innerException = null
    ) {
        parent::__construct(
            sprintf(
                'Unknown lock "%s"',
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

    /**
     * @return ExceptionInterface|null
     */
    public function getInnerException(): ?ExceptionInterface
    {
        return $this->innerException;
    }
}
