<?php

namespace Fastbolt\CommandScheduler\Persistence;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

final class SchemaManager
{
    public const TABLE_NAME_COMMAND_LOG      = 'command_scheduler_logs';
    public const TABLE_NAME_COMMAND_SCHEDULE = 'command_scheduler_schedules';

    private AbstractSchemaManager $schemaManager;

    /**
     * @param Connection $connection
     */
    public function __construct(
        Connection $connection
    ) {
        $this->schemaManager = $connection->createSchemaManager();
    }

    /**
     * @return bool
     */
    public function logTableExists(): bool
    {
        return $this->tableExists(self::TABLE_NAME_COMMAND_LOG);
    }

    /**
     * @param string $table
     *
     * @return bool
     */
    private function tableExists(string $table): bool
    {
        try {
            return $this->schemaManager->tablesExist([$table]);
        } catch (DBALException) {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function scheduleTableExists(): bool
    {
        return $this->tableExists(self::TABLE_NAME_COMMAND_SCHEDULE);
    }
}
