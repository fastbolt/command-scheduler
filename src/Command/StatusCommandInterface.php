<?php

namespace Fastbolt\CommandScheduler\Command;

use Symfony\Component\Console\Command\SignalableCommandInterface;

interface StatusCommandInterface extends SignalableCommandInterface
{
    /**
     * May return anything which can be serialized by the symfony serializer.
     *
     * Serialization is automatically done in `ConsoleAlarmEventSubscriber` and persisted to the database.
     *
     * Please ensure your symfony serializer and value objects are properly configured.
     *
     * @return mixed
     */
    public function getStatus(): mixed;

    /**
     * @return string
     */
    public function getStatusText(): string;

    /**
     * @param mixed $status
     *
     * @return void
     */
    public function setStatus(mixed $status): void;

    /**
     * @param string $statusText
     *
     * @return void
     */
    public function setStatusText(string $statusText): void;

    /**
     * @return int
     */
    public function getAlarmInterval(): int;
}
