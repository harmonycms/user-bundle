<?php

namespace Harmony\Bundle\UserBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;
use Harmony\Bundle\UserBundle\DependencyInjection\Compiler\ValidationPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class HarmonyUserBundle
 *
 * @package Harmony\Bundle\UserBundle
 */
class HarmonyUserBundle extends Bundle
{

    /**
     * Builds the bundle.
     * It is only ever called once when the cache is empty.
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ValidationPass());

        $mappings = [realpath(__DIR__ . '/Resources/config/doctrine-mapping') => 'Harmony\Bundle\UserBundle\Model'];
        if (\class_exists(DoctrineOrmMappingsPass::class) && $container->has('doctrine.orm.default_entity_manager')) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings));
        }

        if (\class_exists(DoctrineMongoDBMappingsPass::class)) {
            $container->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($mappings, []));
        }
    }
}
