<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler\Exception;

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Lock\Exception\ExceptionInterface;

final class UnknownLockException extends RuntimeException
{
    /**
     * @param string                  $lockName
     * @param ExceptionInterface|null $innerException
     */
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
