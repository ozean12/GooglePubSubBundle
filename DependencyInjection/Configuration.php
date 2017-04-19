<?php

namespace Ozean12\GooglePubSubBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ozean12_google_pub_sub');

        $rootNode
            ->children()
                ->scalarNode('project_id')->cannotBeEmpty()->end()
                ->scalarNode('key_file_path')->cannotBeEmpty()->end()
                ->scalarNode('logger_channel')->defaultNull()->end()
                ->scalarNode('topic_suffix')->defaultNull()->end()
                ->arrayNode('topics')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('push_subscriptions')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
