<?php

namespace Fastbolt\CommandScheduler\EventSubscriber;

use Fastbolt\CommandScheduler\Persistence\CommandLogPersister;
use Fastbolt\CommandScheduler\Persistence\CommandLogRegistry;
use Override;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CommandEventSubscriber implements EventSubscriberInterface
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
            ConsoleCommandEvent::class   => 'onConsoleCommand',
            ConsoleTerminateEvent::class => 'onConsoleTerminate',
        ];
    }

    /**
     * @param ConsoleCommandEvent $event
     *
     * @return void
     */
    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        // fail silently
        if (null === ($command = $event->getCommand())) {
            return;
        }

        // fail silently
        if (null === ($commandName = $command->getName())) {
            return;
        }

//        if(null !== ($application = $command->getApplication()) && method_exists()) {}
//
//        $command->getApplication()->setAlarm

        $log = $this->commandLogPersister->createLog($commandName);
        $this->commandLogRegistry->registerItem(spl_object_hash($command), $log);
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
