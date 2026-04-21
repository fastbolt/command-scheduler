<?php

namespace Fastbolt\CommandScheduler\Command;

use Fastbolt\CommandScheduler\Persistence\CommandLogPersister;
use Symfony\Component\Console\Command\Command;
use Symfony\Contracts\Service\Attribute\Required;

class AbstractCommand extends Command {
    private ?CommandLogPersister $commandLogPersister = null;

    /**
     * @param CommandLogPersister $commandLogPersister
     */
    public function setCommandLogPersister(CommandLogPersister $commandLogPersister): void
    {
        $this->commandLogPersister = $commandLogPersister;
    }

    /**
     * @return CommandLogPersister|null
     */
    #[Required]
    public function getCommandLogPersister(): ?CommandLogPersister
    {
        return $this->commandLogPersister;
    }
}
