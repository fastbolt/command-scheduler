<?php

namespace Fastbolt\CommandScheduler\Persistence;

use Fastbolt\CommandScheduler\Entity\CommandLog;

class CommandLogRegistry
{
    private array $logItemsBySplObjectHash = [];

    public function registerItem(string $hash, CommandLog $logItem): void
    {
        $this->logItemsBySplObjectHash[$hash] = $logItem;
    }

    public function getItem(string $hash): ?CommandLog
    {
        return $this->logItemsBySplObjectHash[$hash] ?? null;
    }
}
