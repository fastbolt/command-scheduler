<?php

namespace Fastbolt\CommandScheduler\Lock;

use Fastbolt\CommandScheduler\Exception\AcquireLockException;
use Fastbolt\CommandScheduler\Exception\UnknownLockException;
use Symfony\Component\Lock\Exception\ExceptionInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

class LockRegistry
{
    private array $locks = [];

    public function __construct(
        private readonly LockFactory $lockFactory
    ) {
    }

    public function getLock(string $name): LockInterface
    {
        $lockName               = $this->commandToLogName($name);
        $this->locks[$lockName] = $lock = $this->lockFactory->createLock($lockName);

        if (!$lock->acquire()) {
            throw new AcquireLockException($lockName);
        }

        return $lock;
    }

    public function releaseLock(string $name): void
    {
        $lockName = $this->commandToLogName($name);
        if (
            null === ($lock = $this->locks[$lockName] ?? null)
            || !$lock->isAcquired()
        ) {
            throw new UnknownLockException($lockName);
        }

        try {
            $lock->release();
        } catch (ExceptionInterface $exception) {
            throw new UnknownLockException($lockName, $exception);
        }
    }

    private function commandToLogName(string $name): string
    {
        return preg_replace('/[^a-zA-Z0-9]/', '_', $name);
    }
}
