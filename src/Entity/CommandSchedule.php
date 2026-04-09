<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Fastbolt\CommandScheduler\Repository\CommandScheduleRepository;

#[ORM\Entity(repositoryClass: CommandScheduleRepository::class)]
#[ORM\Table(name: 'fabric_syncs')]
class CommandSchedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id = 0;

    #[ORM\Column(length: 255, unique: true)]
    private string $command;

    #[ORM\Column(length: 255)]
    private string $arguments;

    #[ORM\Column(length: 255)]
    private string $cronExpression;

    #[ORM\Column]
    private bool $enabled;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt;

    /**
     * @var iterable<CommandLog>
     */
    #[ORM\OneToMany(targetEntity: CommandLog::class, mappedBy: 'commandSchedule')]
    private iterable $logs;

    public function __construct(string $command, string $cronExpression, string $arguments = '', bool $enabled = true)
    {
        $this->command        = $command;
        $this->cronExpression = $cronExpression;
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
     * @return string
     */
    public function getArguments(): string
    {
        return $this->arguments;
    }

    /**
     * @return string
     */
    public function getCronExpression(): string
    {
        return $this->cronExpression;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param string $command
     */
    public function setCommand(string $command): void
    {
        $this->command = $command;
    }

    /**
     * @param string $arguments
     */
    public function setArguments(string $arguments): void
    {
        $this->arguments = $arguments;
    }

    /**
     * @param string $cronExpression
     */
    public function setCronExpression(string $cronExpression): void
    {
        $this->cronExpression = $cronExpression;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return iterable<CommandLog>
     */
    public function getLogs(): iterable
    {
        return $this->logs;
    }
}
