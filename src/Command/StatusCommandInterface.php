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
     * Interval to update status in seconds. Every N seconds, the `getStatus` and `getStatusText` methods will be
     * called and the results will be persisted to the database. This is used to update the status of a
     * command while it is running.
     *
     * @return int
     */
    public function getAlarmInterval(): int;
}
