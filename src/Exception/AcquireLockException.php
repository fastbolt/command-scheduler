<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler\Exception;

use Symfony\Component\Console\Exception\RuntimeException;

final class AcquireLockException extends RuntimeException
{
    /**
     * @param string $lockName
     */
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
