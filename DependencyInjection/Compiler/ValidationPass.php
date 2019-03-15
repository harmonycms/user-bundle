<?php

namespace Harmony\Bundle\UserBundle\DependencyInjection\Compiler;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Class ValidationPass
 *
 * @package Harmony\Bundle\UserBundle\DependencyInjection\Compiler
 */
class ValidationPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (class_exists(DoctrineOrmMappingsPass::class)) {
            $container->getDefinition('validator.builder')->addMethodCall('addXmlMapping', [
                dirname(dirname(__DIR__)) . '/Resources/config/validation/orm.xml'
            ]);
        }
    }
}