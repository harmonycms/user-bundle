<?php

namespace Harmony\Bundle\UserBundle\DependencyInjection;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class HarmonyUserExtension
 *
 * @package Harmony\Bundle\UserBundle\DependencyInjection
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

        // get all bundles
        $bundles = $container->getParameter('kernel.bundles');

        $userClass = null;
        if (\class_exists(DoctrineMongoDBMappingsPass::class) && isset($bundles['DoctrineMongoDBBundle'])) {
            $userClass = $config['user_mongodb_class'] ?? null;
        } elseif (\class_exists(DoctrineOrmMappingsPass::class) && isset($bundles['DoctrineBundle'])) {
            $userClass = $config['user_orm_class'] ?? null;
        }

        $container->setParameter('harmony_user.user_class', $userClass);
        $container->setParameter('harmony_user.password_reset.email_from',
            $config['password_reset']['email_from'] ?? null);
        $container->setParameter('harmony_user.password_reset.token_ttl',
            $config['password_reset']['token_ttl'] ?? null);
    }
}
