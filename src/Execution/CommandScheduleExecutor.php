<?php

namespace Fastbolt\CommandScheduler\Execution;

use Exception;
use Fastbolt\CommandScheduler\Entity\CommandSchedule;
use Fastbolt\CommandScheduler\Lock\LockRegistry;
use Fastbolt\CommandScheduler\Persistence\CommandSchedulerLogPersister;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use DateTimeImmutable;

class CommandScheduleExecutor
{
    private ?Application $application = null;

    public function __construct(
        private readonly LockRegistry $lockRegistry,
        private readonly CommandSchedulerLogPersister $persister
    ) {
    }

    public function execute(CommandSchedule $commandSchedule, OutputInterface $output): ?int
    {
        $log    = null;
        $result = null;
        try {
            $this->lockRegistry->getLock($commandName = $commandSchedule->getCommand());
            $log = $this->persister->persistSchedule($commandSchedule);

            $executable   = $this->application->find($commandSchedule->getCommand());
            $commandInput = new StringInput($commandSchedule->getArguments());

            $result = $executable->run($commandInput, $output);
            $log->setReturnCode($result);
            $log->setFinishedAt(new DateTimeImmutable());

            $this->lockRegistry->releaseLock($commandName);
        } catch (Exception $exception) {
            throw $exception;
        } finally {
        }

        return $result;
    }

    public function setApplication(?Application $application): CommandScheduleExecutor
    {
        $this->application = $application;

        return $this;
    }
}
