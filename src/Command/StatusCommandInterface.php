<?php

namespace Fastbolt\CommandScheduler\Command;

interface StatusCommandInterface
{
    public function getStatus(): mixed;

    public function getStatusText(): string;

    public function setStatus(mixed $status): void;

    public function setStatusText(string $statusText): void;

    public function getAlarmInterval(): int;
}
