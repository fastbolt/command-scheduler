<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler\Repository;

use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;
use Fastbolt\CommandScheduler\Entity\CommandLog;
use Symfony\Component\Console\Command\Command;

/**
 * @extends ServiceEntityRepository<CommandLog>
 */
class CommandLogRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommandLog::class);
    }

    /**
     * @return iterable<CommandLog>
     */
    public function findScheduledCommands(): iterable
    {
        return $this->createQueryBuilder('cl')
                    ->leftJoin('cl.commandSchedule', 'c')
                    ->where('cl.startedAt IS NULL')
                    ->orderBy('c.priority', 'ASC')
                    ->addOrderBy('cl.command', 'ASC')
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @return iterable<CommandLog>
     */
    public function findRunningCommands(): iterable
    {
        $yesterday = (new DateTime())
            ->sub(new DateInterval('P1D'))
            ->format('Y-m-d H:i:s');

        return $this->createQueryBuilder('cl')
                    ->leftJoin('cl.commandSchedule', 'c')
                    ->where('cl.startedAt IS NOT NULL')
                    ->andWhere('cl.finishedAt IS NULL')
                    ->andWhere('cl.startedAt >= :yesterday')
                    ->orderBy('cl.startedAt', 'ASC')
                    ->setParameter('yesterday', $yesterday, ParameterType::STRING)
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @return iterable<CommandLog>
     */
    public function findErrorCommands(): iterable
    {
        $yesterday = (new DateTime())
            ->sub(new DateInterval('P1D'))
            ->format('Y-m-d H:i:s');

        return $this->createQueryBuilder('cl')
                    ->where('cl.returnCode <> :successCode')
                    ->andWhere('cl.startedAt >= :yesterday')
                    ->orderBy('cl.startedAt', 'DESC')
                    ->setParameter('yesterday', $yesterday, ParameterType::STRING)
                    ->setParameter('successCode', Command::SUCCESS, ParameterType::INTEGER)
                    ->getQuery()
                    ->getResult();
    }

    public function getLastExecution(string $command): ?CommandLog
    {
        return $this->createQueryBuilder('cl')
                    ->where('cl.command = :command')
                    ->andWhere('cl.startedAt IS NOT NULL')
                    ->andWhere('cl.finishedAt IS NOT NULL')
                    ->orderBy('cl.startedAt', 'DESC')
                    ->setParameter('command', $command, ParameterType::STRING)
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    public function getLastSuccess(string $command): ?CommandLog
    {
        return $this->createQueryBuilder('cl')
                    ->where('cl.command = :command')
                    ->andWhere('cl.startedAt IS NOT NULL')
                    ->andWhere('cl.finishedAt IS NOT NULL')
                    ->andWhere('cl.returnCode = :successCode')
                    ->orderBy('cl.startedAt', 'DESC')
                    ->setParameter('command', $command, ParameterType::STRING)
                    ->setParameter('successCode', Command::SUCCESS, ParameterType::INTEGER)
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();
    }
}
