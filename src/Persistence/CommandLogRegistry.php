<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler\Persistence;

use Fastbolt\CommandScheduler\Entity\CommandLog;

final class CommandLogRegistry
{
    /**
     * @var array<string, CommandLog>
     */
    private array $logItemsBySplObjectHash = [];

    /**
     * @var array<string, true>
     */
    private array $externallyManagedItems = [];

    /**
     * @param string     $hash
     * @param CommandLog $logItem
     *
     * @return void
     */
    public function registerItem(string $hash, CommandLog $logItem, bool $externallyManaged = false): void
    {
        $this->logItemsBySplObjectHash[$hash] = $logItem;

        if ($externallyManaged) {
            $this->externallyManagedItems[$hash] = true;
        } else {
            unset($this->externallyManagedItems[$hash]);
        }
    }

    /**
     * @param string $hash
     *
     * @return CommandLog|null
     */
    public function getItem(string $hash): ?CommandLog
    {
        return $this->logItemsBySplObjectHash[$hash] ?? null;
    }

    /**
     * @param string $hash
     *
     * @return bool
     */
    public function hasItem(string $hash): bool
    {
        return isset($this->logItemsBySplObjectHash[$hash]);
    }

    /**
     * @param string $hash
     *
     * @return bool
     */
    public function isExternallyManaged(string $hash): bool
    {
        return isset($this->externallyManagedItems[$hash]);
    }

    /**
     * @param string $hash
     *
     * @return void
     */
    public function unregisterItem(string $hash): void
    {
        unset(
            $this->logItemsBySplObjectHash[$hash],
            $this->externallyManagedItems[$hash],
        );
    }
}
