<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class CommandSchedulerExtension extends Extension
{
    /**
     * @param array<string, mixed> $configs
     * @param ContainerBuilder     $container
     *
     * @return void
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        // load configuration
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);
        $locator       = new FileLocator(__DIR__ . '/../../config/');
        $loader        = new YamlFileLoader($container, $locator);

        foreach ($config as $key => $value) {
            $container->setParameter('command_scheduler.' . $key, $value);
        }

        $loader->load('services.yaml');
    }
}
