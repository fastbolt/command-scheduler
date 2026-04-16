<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Fastbolt\CommandScheduler\Entity\CommandSchedule;

/**
 * @extends ServiceEntityRepository<CommandSchedule>
 */
final class CommandScheduleRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommandSchedule::class);
    }

    /**
     * @return CommandSchedule[]
     */
    public function findEnabledSchedules(): array
    {
        return $this->findBy(['enabled' => true]);
    }
}
