<?php

namespace Fastbolt\CommandScheduler\Persistence;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

class SchemaManager
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
     * @param string $table
     *
     * @return bool
     */
    private function tableExists(string $table): bool
    {
        return $this->schemaManager->tablesExist([$table]);
    }

    /**
     * @return bool
     */
    public function logTableExists(): bool
    {
        return $this->tableExists(self::TABLE_NAME_COMMAND_LOG);
    }

    /**
     * @return bool
     */
    public function scheduleTableExists(): bool
    {
        return $this->tableExists(self::TABLE_NAME_COMMAND_SCHEDULE);
    }
}
