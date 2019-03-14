<?php

namespace Harmony\UserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class HarmonyUserExtension
 *
 * @package Harmony\UserBundle\DependencyInjection
 */
class HarmonyUserExtension extends Extension
{

    /**
     * Loads a specific configuration.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $container->setParameter('harmony_user.user_class',
            isset($config['user_class']) ? $config['user_class'] : null);
        $container->setParameter('harmony_user.password_reset.email_from',
            isset($config['password_reset']['email_from']) ? $config['password_reset']['email_from'] : null);
        $container->setParameter('harmony_user.password_reset.token_ttl',
            isset($config['password_reset']['token_ttl']) ? $config['password_reset']['token_ttl'] : null);
    }
}