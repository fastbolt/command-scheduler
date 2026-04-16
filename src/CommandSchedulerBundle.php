<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Override;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CommandSchedulerBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     *
     * @return void
     */
    #[Override]
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createAttributeMappingDriver(
                ['Fastbolt\\CommandScheduler\\Entity'],
                [__DIR__ . '/Entity/']
            )
        );
    }
}
