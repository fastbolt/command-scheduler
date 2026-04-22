<?php

namespace Fastbolt\CommandScheduler\EventSubscriber;

use Fastbolt\CommandScheduler\Persistence\CommandLogPersister;
use Fastbolt\CommandScheduler\Persistence\CommandLogRegistry;
use Override;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ConsoleTerminateEventSubscriber implements EventSubscriberInterface
{
    /**
     * @param CommandLogRegistry  $commandLogRegistry
     * @param CommandLogPersister $commandLogPersister
     */
    public function __construct(
        private readonly CommandLogRegistry $commandLogRegistry,
        private readonly CommandLogPersister $commandLogPersister,
    ) {
    }

    /**
     * @return array<class-string, string>
     */
    #[Override]
    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleTerminateEvent::class => 'onConsoleTerminate',
        ];
    }

    /**
     * @param ConsoleTerminateEvent $event
     *
     * @return void
     */
    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        // fail silently
        if (null === ($command = $event->getCommand())) {
            return;
        }

        // fail silently
        if (null === ($log = $this->commandLogRegistry->getItem(spl_object_hash($command)))) {
            return;
        }

        $this->commandLogPersister->finishLog($log, $event->getExitCode());
    }
}
