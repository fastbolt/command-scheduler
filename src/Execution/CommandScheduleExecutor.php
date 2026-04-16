<?php

namespace Fastbolt\CommandScheduler\Execution;

use Exception;
use Fastbolt\CommandScheduler\Entity\CommandLog;
use Fastbolt\CommandScheduler\Lock\LockRegistry;
use Fastbolt\CommandScheduler\Persistence\CommandLogPersister;
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
        private readonly CommandLogPersister $persister
    ) {
    }

    /**
     * @param CommandLog   $commandLog
     * @param SymfonyStyle $output
     *
     * @return int|null
     */
    public function execute(CommandLog $commandLog, SymfonyStyle $output): ?int
    {
        $exception   = null;
        $lock        = null;
        $command     = $commandLog->getCommandSchedule();
        $commandName = null;
        $result=null;

        try {
            $lock = $this->lockRegistry->getLock($commandName = $commandLog->getCommand());

            // set started
            $this->persister->startLog($commandLog);

            // find executable from application stack
            $executable = $this->application->find($commandLog->getCommand());

            $arguments = $command ? $command->getArguments() : '';

            // create command line input
            $commandInput = new StringInput($arguments);

            // run executable
            $result = $executable->run($commandInput, $output);
        } catch (Exception $exception) {
            $result = CommandLog::COMMAND_RETURN_EXCEPTION;

            $output->error(
                sprintf(
                    'Exception "%s" while executing command "%s": %s',
                    get_class($exception),
                    $commandLog->getCommand(),
                    $exception->getMessage()
                )
            );
        } finally {
            // Update log entry if exists
            $this->persister->finishLog($commandLog, $result);

            // release lock present
            if (null !== $lock && null !== $commandName) {
                $this->lockRegistry->releaseLock($commandName);
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
