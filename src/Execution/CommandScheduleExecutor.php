<?php

namespace Fastbolt\CommandScheduler\Execution;

use Fastbolt\CommandScheduler\Persistence\CommandLogRegistry;
use Throwable;
use Fastbolt\CommandScheduler\Entity\CommandLog;
use Fastbolt\CommandScheduler\Lock\LockRegistry;
use Fastbolt\CommandScheduler\Persistence\CommandLogPersister;
use RuntimeException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CommandScheduleExecutor
{
    private ?Application $application = null;

    /**
     * @param LockRegistry        $lockRegistry
     * @param CommandLogPersister $persister
     */
    public function __construct(
        private readonly LockRegistry $lockRegistry,
        private readonly CommandLogPersister $persister,
        private readonly CommandLogRegistry $commandLogRegistry,
    ) {
    }

    /**
     * @param CommandLog   $commandLog
     * @param SymfonyStyle $output
     *
     * @return int
     */
    public function execute(CommandLog $commandLog, SymfonyStyle $output): int
    {
        if (null === ($application = $this->application)) {
            throw new RuntimeException('Application object not set. Please set it before executing commands.');
        }

        $lock        = null;
        $commandHash = null;
        $commandName = $commandLog->getCommand();
        $result      = CommandLog::COMMAND_RETURN_EXCEPTION;
        $exception   = null;

        try {
            // set started
            $this->persister->startLog($commandLog);

            $consoleCommand = $application->find($commandName);
            $commandHash    = spl_object_hash($consoleCommand);

            $this->commandLogRegistry->registerItem(
                $commandHash,
                $commandLog,
                true,
            );

            $command = $commandLog->getCommandSchedule();

            $lock      = $this->lockRegistry->getLock($commandName = $commandLog->getCommand());
            $arguments = $command ? $command->getArguments() : '';

            // create command line input
            $commandInput = new StringInput($commandName . ' ' . $arguments);

            // run executable
            $result = $application->run($commandInput, $output);
        } catch (Throwable $exception) {
            $output->error(
                sprintf(
                    'Exception "%s" while executing command "%s": %s',
                    get_class($exception),
                    $commandName,
                    $exception->getMessage()
                )
            );
        } finally {
            try {
                // Update log entry if exists
                $this->persister->finishLog($commandLog, $result);
            } catch (Throwable $throwable) {
                $output->error(
                    sprintf(
                        'Could not finish log %d for command "%s": %s',
                        $commandLog->getId(),
                        $commandName,
                        $throwable->getMessage(),
                    )
                );
            } finally {
                if (null !== $commandHash) {
                    $this->commandLogRegistry->unregisterItem(
                        $commandHash,
                    );
                }
                // release lock present
                if (null !== $lock) {
                    try {
                        $this->lockRegistry->releaseLock(
                            $commandName,
                        );
                    } catch (Throwable $throwable) {
                        $output->error(
                            sprintf(
                                'Could not release lock for command "%s": %s',
                                $commandName,
                                $throwable->getMessage(),
                            )
                        );
                    }
                }
            }
        }

        // throw previously caught exception
        if ($exception) {
//            throw $exception;
        }

        return $result;
    }

    /**
     * @param Application|null $application
     *
     * @return $this
     */
    public function setApplication(?Application $application): CommandScheduleExecutor
    {
        $this->application = $application;

        return $this;
    }
}
