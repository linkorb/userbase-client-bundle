<?php

namespace UserBase\ClientBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class UserBaseClientExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.xml');

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $httpClientDefn = $container->getDefinition('user_base_client.http_client');
        $httpClientDefn->replaceArgument('$url', $config['http_client']['url']);
        $httpClientDefn->replaceArgument('$username', $config['http_client']['username']);
        $httpClientDefn->replaceArgument('$password', $config['http_client']['password']);
        $httpClientDefn->replaceArgument('$partition', $config['http_client']['partition']);

        $userProvidertDefn = $container->getDefinition('user_base_client.user_provider');
        $userProvidertDefn->replaceArgument('$shouldRefresh', $config['user_provider']['always_refresh_user']);
    }
}
