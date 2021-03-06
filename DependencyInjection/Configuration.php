<?php

namespace Harmony\Bundle\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('harmony_user');
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('user_class')
                    ->info('User class interface (FQDN)')
                    ->defaultValue(UserInterface::class)
                ->end()
                ->arrayNode('password_reset')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('email_from')
                            ->info('Sender of password reset emails')
                            ->example('John Doe <jdoe@domain.tld>')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('token_ttl')
                            ->info('Token TTL (seconds)')
                            ->defaultValue(7200)
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
