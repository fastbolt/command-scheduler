<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler\Entity;

use Cron\CronExpression;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Fastbolt\CommandScheduler\Repository\CommandScheduleRepository;

#[ORM\Entity(repositoryClass: CommandScheduleRepository::class)]
#[ORM\Table(name: 'command_scheduler_schedules')]
#[ORM\UniqueConstraint(name: 'unique_command_arguments', columns: ['command', 'arguments'])]
class CommandSchedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id = 0;

    #[ORM\Column(length: 255)]
    private string $command;

    #[ORM\Column(length: 255)]
    private string $arguments;

    #[ORM\Column(length: 255)]
    private string $cronExpression;

    #[ORM\Column]
    private int $priority;

    #[ORM\Column]
    private bool $enabled;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    /**
     * @var iterable<CommandLog>
     */
    #[ORM\OneToMany(targetEntity: CommandLog::class, mappedBy: 'commandSchedule')]
    private iterable $logs;

    /**
     * @param string $command
     * @param string $cronExpression
     * @param int    $priority
     * @param string $arguments
     * @param bool   $enabled
     */
    public function __construct(
        string $command,
        string $cronExpression,
        int $priority,
        string $arguments = '',
        bool $enabled = true
    ) {
        $this->command        = $command;
        $this->cronExpression = $cronExpression;
        $this->priority       = $priority;
        $this->arguments      = $arguments;
        $this->enabled        = $enabled;
        $this->logs           = new ArrayCollection();
        $this->createdAt      = new DateTimeImmutable();
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
     * @param string $command
     */
    public function setCommand(string $command): void
    {
        $this->command = $command;
    }

    /**
     * @return string
     */
    public function getArguments(): string
    {
        return $this->arguments;
    }

    /**
     * @param string $arguments
     */
    public function setArguments(string $arguments): void
    {
        $this->arguments = $arguments;
    }

    /**
     * @return string
     */
    public function getCronExpression(): string
    {
        return $this->cronExpression;
    }

    /**
     * @param string $cronExpression
     */
    public function setCronExpression(string $cronExpression): void
    {
        $this->cronExpression = $cronExpression;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return iterable<CommandLog>
     */
    public function getLogs(): iterable
    {
        return $this->logs;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return md5($this->command . $this->arguments);
    }

    /**
     * @return DateTimeInterface
     */
    public function getNextRun(): ?DateTimeInterface
    {
        if (!CronExpression::isValidExpression($expression = $this->getCronExpression())) {
            return null;
        }

        if (!$this->isEnabled()) {
            return null;
        }

        $expr = new CronExpression($expression);

        return $expr->getNextRunDate();
    }
}
