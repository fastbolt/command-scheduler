<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Fastbolt\CommandScheduler\Repository\CommandLogRepository;

#[ORM\Entity(repositoryClass: CommandLogRepository::class)]
#[ORM\Table(name: 'command_scheduler_logs')]
#[ORM\HasLifecycleCallbacks]
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
    private DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $startedAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $finishedAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $changedAt;

    #[ORM\Column(nullable: true)]
    private ?int $returnCode = null;

    #[ORM\Column(nullable: true)]
    private ?string $userIdentifier = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $status = '';

    #[ORM\Column(length: 255)]
    private string $statusText = '';

    /**
     * @param string               $command
     * @param CommandSchedule|null $commandSchedule
     */
    public function __construct(string $command, ?CommandSchedule $commandSchedule = null)
    {
        $this->command         = $command;
        $this->commandSchedule = $commandSchedule;
        $this->createdAt       = new DateTimeImmutable();
        $this->changedAt       = new DateTimeImmutable();
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
    public function getStartedAt(): ?DateTimeImmutable
    {
        return $this->startedAt;
    }

    /**
     * @param DateTimeImmutable $startedAt
     */
    public function setStartedAt(DateTimeImmutable $startedAt): void
    {
        $this->startedAt = $startedAt;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getFinishedAt(): ?DateTimeImmutable
    {
        return $this->finishedAt;
    }

    /**
     * @param DateTimeImmutable $finishedAt
     */
    public function setFinishedAt(DateTimeImmutable $finishedAt): void
    {
        $this->finishedAt = $finishedAt;
    }

    /**
     * @return int|null
     */
    public function getReturnCode(): ?int
    {
        return $this->returnCode;
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

    /**
     * @return string
     */
    public function getUserIdentifier(): ?string
    {
        return $this->userIdentifier;
    }

    /**
     * @param string|null $userIdentifier
     */
    public function setUserIdentifier(?string $userIdentifier): void
    {
        $this->userIdentifier = $userIdentifier;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatusText(): string
    {
        return $this->statusText;
    }

    /**
     * @param string $statusText
     */
    public function setStatusText(string $statusText): void
    {
        $this->statusText = $statusText;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getChangedAt(): ?DateTimeImmutable
    {
        return $this->changedAt;
    }

    /**
     * @param DateTimeImmutable|null $changedAt
     */
    public function setChangedAt(?DateTimeImmutable $changedAt): void
    {
        $this->changedAt = $changedAt;
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->changedAt = new DateTimeImmutable();
    }
}
