<?php

namespace Anh\Bundle\ContentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     * Config options for each section:
     * - category
     * - slug
     * - publishedSince
     * - assets - [asset=""]
     * - tags
     * - preview - [preview] [proceed]
     * - image - allow separate image for document (usualy used with preview)
     * - comments
     * - route - route name for view document, anh_content_{section}_view by default
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('anh_content');

        $rootNode
            ->children()
                ->arrayNode('sections')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->booleanNode('category')
                                ->defaultFalse()
                            ->end()
                            ->booleanNode('slug')
                                ->defaultFalse()
                            ->end()
                            ->booleanNode('publishedSince')
                                ->defaultFalse()
                            ->end()
                            ->booleanNode('assets')
                                ->defaultFalse()
                            ->end()
                            ->booleanNode('tags')
                                ->defaultFalse()
                            ->end()
                            ->booleanNode('preview')
                                ->defaultFalse()
                            ->end()
                            ->booleanNode('image')
                                ->defaultFalse()
                            ->end()
                            ->booleanNode('comments')
                                ->defaultFalse()
                            ->end()
                            ->scalarNode('route')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
