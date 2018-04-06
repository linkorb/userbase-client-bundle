<?php

namespace UserBase\ClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('user_base_client');

        $rootNode
            ->children()
                ->arrayNode('http_client')
                    ->isRequired()
                    ->children()
                        ->scalarNode('url')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->info('A DSN or URL of the UserBase Authentication endpoint.')
                        ->end()
                        ->scalarNode('username')
                            ->info('Not required if the value of "url" is a DSN.')
                            ->defaultNull()
                        ->end()
                        ->scalarNode('password')
                            ->info('Not required if the value of "url" is a DSN.')
                            ->defaultNull()
                        ->end()
                        ->scalarNode('partition')
                            ->defaultValue('dev')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('user_provider')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('always_refresh_user')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
