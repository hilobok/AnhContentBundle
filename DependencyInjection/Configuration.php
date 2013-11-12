<?php

namespace Anh\ContentBundle\DependencyInjection;

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
     *
     * assets_dir - absolute path to directory where assets are stored
     * Config options for each section:
     * - category
     * - slug
     * - publishedSince
     * - assets - [asset=""]
     * - tags
     * - preview - [preview] [proceed]
     * - image - allow separate image for paper (usualy used with preview)
     * - comments
     * - filter - default filter for assets
     * - routes
     *      - paper - route name for view paper, anh_content_{section}_paper by default
     *      - papers - route name for list papers, anh_content_{section}_papers by default
     *      - category - route name for view category, anh_content_{section}_category by default
     *      - categories - route name for list categories, anh_content_{section}_categories by default
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('anh_content');

        $rootNode
            ->children()
                ->scalarNode('assets_dir')
                    ->info('Absolute path to directory where assets are stored.')
                    ->defaultValue('%kernel.root_dir%/../web/media/content')
                ->end()
                ->arrayNode('sections')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->booleanNode('category')
                                ->defaultFalse()
                                ->info('Paper can have category.')
                            ->end()
                            ->booleanNode('slug')
                                ->defaultFalse()
                                ->info('Paper can have slug.')
                            ->end()
                            ->booleanNode('publishedSince')
                                ->defaultFalse()
                                ->info('Enable delayed publication.')
                            ->end()
                            ->booleanNode('assets')
                                ->defaultFalse()
                                ->info('Enable assets for papers in this section.')
                            ->end()
                            ->booleanNode('tags')
                                ->defaultFalse()
                                ->info('Enable tagging for papers in this section.')
                            ->end()
                            ->booleanNode('preview')
                                ->defaultFalse()
                                ->info('Enable preview for papers in this section.')
                            ->end()
                            ->booleanNode('image')
                                ->defaultFalse()
                                ->info('Enable preview image for papers in this section.')
                            ->end()
                            ->booleanNode('comments')
                                ->defaultFalse()
                                ->info('Enable comments for papers in this section.')
                            ->end()
                            ->scalarNode('filter')
                                ->info('Default filter for assets.')
                            ->end()
                            ->arrayNode('routes')
                                ->info('Routes for this section. Assigned automaticaly if empty.')
                                ->children()
                                    ->scalarNode('paper')
                                        ->info('Route name for view paper. Defaults to anh_content_{section}_paper')
                                    ->end()
                                    ->scalarNode('papers')
                                        ->info('Route name for list papers. Defaults to anh_content_{section}_papers')
                                    ->end()
                                    ->scalarNode('category')
                                        ->info('Route name for view category. Defaults to anh_content_{section}_category')
                                    ->end()
                                    ->scalarNode('categories')
                                        ->info('Route name for list categories. Defaults to anh_content_{section}_categories')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
