<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler\Persistence;

use Fastbolt\CommandScheduler\Entity\CommandLog;

class CommandLogRegistry
{
    private array $logItemsBySplObjectHash = [];

    /**
     * @param string     $hash
     * @param CommandLog $logItem
     *
     * @return void
     */
    public function registerItem(string $hash, CommandLog $logItem): void
    {
        $this->logItemsBySplObjectHash[$hash] = $logItem;
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
}
