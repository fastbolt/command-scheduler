<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler\Provider;

use DateTimeInterface;
use Fastbolt\CommandScheduler\Entity\CommandLog;
use Fastbolt\CommandScheduler\Persistence\SchemaManager;
use Fastbolt\CommandScheduler\Repository\CommandLogRepository;
use Symfony\Component\Console\Command\Command;

/**
 * @api
 */
class CommandLogProvider
{
    /**
     * @param CommandLogRepository $commandLogRepository
     */
    public function __construct(
        private readonly CommandLogRepository $commandLogRepository,
        private readonly SchemaManager $schemaManager
    ) {
    }

    /**
     * Method for getting a list of all currently running commands.
     * "Running" are all commandLog entries where startedAt IS NOT NULL and finishedAt is null, ordered by startedAt
     * ascending.
     *
     * @return CommandLog[]
     */
    public function getRunningCommands(): array
    {
        if (!$this->schemaManager->logTableExists()) {
            return [];
        }

        return $this->commandLogRepository->findRunningCommands();
    }

    /**
     * @return CommandLog[]
     */
    public function getErrors(): array
    {
        if (!$this->schemaManager->logTableExists()) {
            return [];
        }

        return $this->commandLogRepository->findErrorCommands();
    }

    /**
     * @param string $command
     *
     * @return bool
     */
    public function hasLastExecutionFailed(string $command): bool
    {
        if (!$this->schemaManager->logTableExists()) {
            return false;
        }
        if (null === ($lastExecution = $this->commandLogRepository->getLastExecution($command))) {
            return false;
        }

        return $lastExecution->getReturnCode() !== Command::SUCCESS;
    }

    /**
     * @return array<string,string>
     */
    public function getScheduledCommandIdentifiers(): array
    {
        if (!$this->schemaManager->logTableExists()) {
            return [];
        }

        $result = array_filter(
            array_unique(
                array_map(
                    static function (CommandLog $commandLog): ?string {
                        if (null === $commandLog->getCommandSchedule()) {
                            return null;
                        }

                        return $commandLog->getCommandSchedule()?->getIdentifier();
                    },
                    $this->getScheduledCommands()
                )
            )
        );

        return array_combine($result, $result);
    }

    /**
     * Method for getting a list of all currently scheduled commands.
     * "Scheduled" are all commandLog entries where startedAt IS NULL, ordered by priority ascending.
     *
     * @return CommandLog[]
     */
    public function getScheduledCommands(): array
    {
        if (!$this->schemaManager->logTableExists()) {
            return [];
        }

        return $this->commandLogRepository->findScheduledCommands();
    }

    /**
     * @param string $command
     *
     * @return CommandLog|null
     */
    public function getLastSuccess(string $command): ?CommandLog
    {
        if (!$this->schemaManager->logTableExists()) {
            return null;
        }

        return $this->commandLogRepository->getLastSuccess($command);
    }

    /**
     * @param string            $command
     * @param DateTimeInterface $first
     * @param DateTimeInterface $last
     *
     * @return int
     */
    public function getNumErrors(string $command, DateTimeInterface $first, DateTimeInterface $last): int
    {
        if (!$this->schemaManager->logTableExists()) {
            return 0;
        }

        return $this->commandLogRepository->getNumErrors($command, $first, $last);
    }

    /**
     * @return CommandLog[]
     */
    public function getScheduledAndRunningCommands(): array
    {
        if (!$this->schemaManager->logTableExists()) {
            return [];
        }

        return $this->commandLogRepository->findScheduledAndRunningCommands();
    }

    /**
     * Liefert alle CommandLog-Einträge.
     *
     * @return CommandLog[]
     */
    public function getAllLogs(): array
    {
        if (!$this->schemaManager->logTableExists()) {
            return [];
        }

        return $this->commandLogRepository->findBy([], ['createdAt' => 'DESC', 'startedAt' => 'DESC']);
    }

    /**
     * Gibt die Anzahl aller CommandLog-Einträge zurück.
     *
     * @return int
     */
    public function getNumLogs(): int
    {
        if (!$this->schemaManager->logTableExists()) {
            return 0;
        }

        return $this->commandLogRepository->count([]);
    }
}
