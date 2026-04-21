<?php

namespace Fastbolt\CommandScheduler\DependencyInjection\CompilerPasses;

use Fastbolt\CommandScheduler\Persistence\CommandLogRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class StatusCommandCompilerPass implements CompilerPassInterface {
    public function process(ContainerBuilder $container)
    {
        if(!$container->has(CommandLogRegistry::class)) {
            return;
        }

        $registry =
    }
}
