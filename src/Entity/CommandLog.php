<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Fastbolt\CommandScheduler\Repository\CommandLogRepository;

#[ORM\Entity(repositoryClass: CommandLogRepository::class)]
#[ORM\Table(name: 'command_scheduler_logs')]
class CommandLog
{
    public const COMMAND_RETURN_EXCEPTION = -1;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id = 0;

    #[ORM\Column(length: 255)]
    private string $command;

    #[ORM\ManyToOne(targetEntity: CommandSchedule::class, inversedBy: 'logs')]
    private ?CommandSchedule $commandSchedule;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $startedAt;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $finishedAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $returnCode = null;

    public function __construct(string $command, ?CommandSchedule $commandSchedule = null)
    {
        $this->command         = $command;
        $this->commandSchedule = $commandSchedule;
        $this->createdAt       = new DateTimeImmutable();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @return CommandSchedule|null
     */
    public function getCommandSchedule(): ?CommandSchedule
    {
        return $this->commandSchedule;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getStartedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getFinishedAt(): ?DateTimeImmutable
    {
        return $this->finishedAt;
    }

    /**
     * @return int|null
     */
    public function getReturnCode(): ?int
    {
        return $this->returnCode;
    }

    /**
     * @param DateTimeImmutable $startedAt
     */
    public function setStartedAt(DateTimeImmutable $startedAt): void
    {
        $this->startedAt = $startedAt;
    }

    /**
     * @param DateTimeImmutable $finishedAt
     */
    public function setFinishedAt(DateTimeImmutable $finishedAt): void
    {
        $this->finishedAt = $finishedAt;
    }

    /**
     * @param int|null $returnCode
     */
    public function setReturnCode(?int $returnCode): void
    {
        $this->returnCode = $returnCode;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }
}
