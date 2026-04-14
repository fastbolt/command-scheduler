<?php

namespace Fastbolt\CommandScheduler\Execution;

use Exception;
use Fastbolt\CommandScheduler\Entity\CommandLog;
use Fastbolt\CommandScheduler\Lock\LockRegistry;
use Fastbolt\CommandScheduler\Persistence\CommandSchedulerLogPersister;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Console\Style\SymfonyStyle;

class CommandScheduleExecutor
{
    private ?Application $application = null;

    public function __construct(
        private readonly LockRegistry $lockRegistry,
        private readonly CommandSchedulerLogPersister $persister
    ) {
    }

    public function execute(CommandLog $commandLog, SymfonyStyle $output): ?int
    {
        $exception = null;
        $lock      = null;
        $command   = $commandLog->getCommandSchedule();

        try {
            $lock = $this->lockRegistry->getLock($commandName = $commandLog->getCommand());

            // set started
            $this->persister->startLog($commandLog);

            // find executable from application stack
            $executable = $this->application->find($commandLog->getCommand());

            // create command line input
            $commandInput = new StringInput($command?->getArguments() ?: '');

            // run executable
            $result = $executable->run($commandInput, $output);
        } catch (Exception $exception) {
            $result = CommandLog::COMMAND_RETURN_EXCEPTION;

            $output->error(sprintf('Exception while executing command "%s": %s', $commandLog->getCommand(), $exception->getMessage()));
        } finally {
            // Update log entry if exists
            $this->persister->finishLog($commandLog, $result);

            // release lock present
            if (null !== $lock) {
                $this->lockRegistry->releaseLock($commandName);
            }
        }

        // throw previously caught exception
        if ($exception) {
//            throw $exception;
        }

        return $result;
    }

    public function setApplication(?Application $application): CommandScheduleExecutor
    {
        $this->application = $application;

        return $this;
    }
}
