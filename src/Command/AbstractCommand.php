<?php

namespace Fastbolt\CommandScheduler\Command;

use Symfony\Component\Console\Command\Command;

abstract class AbstractCommand extends Command implements LockingCommandInterface {

}
