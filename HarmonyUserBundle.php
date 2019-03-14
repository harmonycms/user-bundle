<?php

namespace Harmony\UserBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Harmony\UserBundle\DependencyInjection\Compiler\ValidationPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class HarmonyUserBundle
 *
 * @package Harmony\UserBundle
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

        $mappings = [realpath(__DIR__ . '/Resources/config/doctrine-mapping') => 'Harmony\UserBundle\Model\User'];
        if (class_exists(DoctrineOrmMappingsPass::class)) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings));
        }
    }
}
