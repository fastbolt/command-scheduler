<?php

namespace Fastbolt\CommandScheduler\EventSubscriber;

use Fastbolt\CommandScheduler\Persistence\CommandLogPersister;
use Fastbolt\CommandScheduler\Persistence\CommandLogRegistry;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommandEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly CommandLogRegistry $commandLogRegistry,
        private readonly CommandLogPersister $commandLogPersister,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleCommandEvent::class   => 'onConsoleCommand',
            ConsoleTerminateEvent::class => 'onConsoleTerminate',
        ];
    }

    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        if (null === ($command = $event->getCommand()->getName())) {
            return;
        }

        $log = $this->commandLogPersister->createLog($command);
        $this->commandLogRegistry->registerItem(spl_object_hash($event->getCommand()), $log);
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        $log = $this->commandLogRegistry->getItem(spl_object_hash($event->getCommand()));
        $this->commandLogPersister->finishLog($log, $event->getExitCode());
    }
}
