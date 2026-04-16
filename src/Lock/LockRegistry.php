<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler\Lock;

use Fastbolt\CommandScheduler\Exception\AcquireLockException;
use Fastbolt\CommandScheduler\Exception\UnknownLockException;
use Symfony\Component\Lock\Exception\ExceptionInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

class LockRegistry
{
    private array $locks = [];

    /**
     * @param LockFactory $lockFactory
     */
    public function __construct(
        private readonly LockFactory $lockFactory
    ) {
    }

    /**
     * @param string $name
     *
     * @return LockInterface
     */
    public function getLock(string $name): LockInterface
    {
        $lockName               = $this->commandToLogName($name);
        $this->locks[$lockName] = $lock = $this->lockFactory->createLock($lockName);

        if (!$lock->acquire()) {
            throw new AcquireLockException($lockName);
        }

        return $lock;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function commandToLogName(string $name): string
    {
        return preg_replace('/[^a-zA-Z0-9]/', '_', $name);
    }

    /**
     * @param string $name
     *
     * @return void
     */
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
}
