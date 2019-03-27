<?php

namespace Harmony\Bundle\UserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use function array_filter;
use function array_shift;

/**
 * Class HarmonyUserExtension
 *
 * @package Harmony\Bundle\UserBundle\DependencyInjection
 */
class HarmonyUserExtension extends Extension implements PrependExtensionInterface
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

        $container->setParameter('harmony_user.user_class', $config['user_class']);
        $container->setParameter('harmony_user.password_reset.email_from',
            $config['password_reset']['email_from'] ?? null);
        $container->setParameter('harmony_user.password_reset.token_ttl', $config['password_reset']['token_ttl']);
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        // get all bundles
        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['HarmonyAdminBundle'])) {
            $loader->load('admin.yaml');
            $config = array_filter($container->getExtensionConfig('harmony_admin'));
            $container->setParameter('harmony_admin.config', ['harmony_admin' => array_shift($config)]);
        }
    }
}
