<?php

namespace Fastbolt\CommandScheduler\EventSubscriber;

use Fastbolt\CommandScheduler\Command\StatusCommandInterface;
use Fastbolt\CommandScheduler\Persistence\CommandLogPersister;
use Fastbolt\CommandScheduler\Persistence\CommandLogRegistry;
use Override;
use Symfony\Component\Console\Event\ConsoleAlarmEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class ConsoleAlarmEventSubscriber implements EventSubscriberInterface
{
    /**
     * @param CommandLogRegistry  $commandLogRegistry
     * @param CommandLogPersister $commandLogPersister
     * @param SerializerInterface $serializer
     */
    public function __construct(
        private readonly CommandLogRegistry $commandLogRegistry,
        private readonly CommandLogPersister $commandLogPersister,
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * @return array<class-string, string>
     */
    #[Override]
    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleAlarmEvent::class => 'onConsoleAlarm',
        ];
    }

    /**
     * @param ConsoleAlarmEvent $event
     *
     * @return void
     */
    public function onConsoleAlarm(ConsoleAlarmEvent $event): void
    {
        // fail silently
        if (null === ($command = $event->getCommand())) {
            return;
        }

        // fail silently
        if (null === ($commandName = $command->getName())) {
            return;
        }

        // we only can check / persist status on commands implementing our interface
        if (!$command instanceof StatusCommandInterface) {
            return;
        }

        if (null === ($log = $this->commandLogRegistry->getItem(spl_object_hash($command)))) {
            return;
        }

        $status     = $this->serializer->serialize($command->getStatus(), 'json');
        $statusText = $command->getStatusText();

        $this->commandLogPersister->updateStatus($log, $status, $statusText);
    }
}
