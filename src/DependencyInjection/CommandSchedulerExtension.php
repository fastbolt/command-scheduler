<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler\DependencyInjection;

use Override;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CommandSchedulerExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array<array<mixed>> $configs
     *
     * @return void
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    #[Override]
    public function load(array $configs, ContainerBuilder $container): void
    {
        // load configuration
        $locator = new FileLocator(__DIR__ . '/../Resources/config/');
        $loader  = new YamlFileLoader($container, $locator);
        $loader->load('services.yaml');
    }
}
