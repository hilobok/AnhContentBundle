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
     * - publishedSince
     * - tags
     * - meta - enable meta [author, description, keywords]
     * - routes
     *      - paper - route name for view paper, anh_content_{section}_paper by default
     *      - papers - route name for list papers, anh_content_{section}_papers by default
     *      - category - route name for view category, anh_content_{section}_category by default
     *      - categories - route name for list categories, anh_content_{section}_categories by default
     * - feeds
     *      name
     *          - conditions: {}
     *          - options: {}
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
                ->arrayNode('assets_mime_types')
                    ->info('Allowed mime types for assets.')
                    ->defaultValue(array(
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'image/bmp'
                    ))
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->scalarNode('assets_max_size')
                    ->info('Assets max file size.')
                    ->defaultValue(5242880) // 5MB
                ->end()
                ->arrayNode('sections')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->booleanNode('hidden')
                                ->defaultFalse()
                                ->info('Hide this section in admin area.')
                            ->end()
                            ->booleanNode('category')
                                ->defaultFalse()
                                ->info('Paper can have category.')
                            ->end()
                            ->booleanNode('publishedSince')
                                ->defaultFalse()
                                ->info('Enable delayed publication.')
                            ->end()
                            ->booleanNode('tags')
                                ->defaultFalse()
                                ->info('Enable tagging for papers in this section.')
                            ->end()
                            ->booleanNode('meta')
                                ->defaultFalse()
                                ->info('Enable meta [author, description, keywords] for papers in this section.')
                            ->end()
                            ->arrayNode('routes')
                                ->info('Routes for this section (paper, papers, category, categories). Assigned automaticaly if empty.')
                                ->useAttributeAsKey('name')
                                ->prototype('scalar')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('feeds')
                    ->info('Feeds for content.')
                    ->useAttributeAsKey('name')
                    ->defaultValue(array())
                    ->prototype('array')
                        ->children()
                            ->arrayNode('conditions')
                                ->defaultValue(array())
                                ->prototype('variable')
                                ->end()
                            ->end()
                            ->arrayNode('options')
                                ->defaultValue(array())
                                ->prototype('variable')
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
