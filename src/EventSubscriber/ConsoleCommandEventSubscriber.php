<?php

namespace Fastbolt\CommandScheduler\EventSubscriber;

use Fastbolt\CommandScheduler\Command\StatusCommandInterface;
use Fastbolt\CommandScheduler\Persistence\CommandLogPersister;
use Fastbolt\CommandScheduler\Persistence\CommandLogRegistry;
use Override;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ConsoleCommandEventSubscriber implements EventSubscriberInterface
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
            ConsoleCommandEvent::class => 'onConsoleCommand',
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

        /** @var Application|null $application */
        $application = $command->getApplication();

        if ($command instanceof StatusCommandInterface && null !== $application) {
            $application->setAutoExit(false);
            $interval = $command->getAlarmInterval();
            if (($output = $event->getOutput())->isVerbose()) {
                $output->writeln(
                    sprintf('<info>%s</info>', sprintf('Setting update interval to %s seconds', $interval))
                );
            }

            $application->setAlarmInterval($interval);
        }

        $commandHash = spl_object_hash($command);

        if ($this->commandLogRegistry->hasItem($commandHash)) {
            return;
        }

        if (null !== ($log = $this->commandLogPersister->createLog($commandName))) {
            $this->commandLogRegistry->registerItem($commandHash, $log);
        }
    }
}
