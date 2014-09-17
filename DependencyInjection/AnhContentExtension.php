<?php

namespace Anh\ContentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AnhContentExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (empty($config['sections'])) {
            throw new \InvalidArgumentException('Define at least one section for AnhContentBundle.');
        }

        $options = array(
            'show_categories' => false,
            'show_tags' => false
        );

        foreach ($config['sections'] as $section => &$flags) {
            $flags['routes'] = (isset($flags['routes']) ? $flags['routes'] : array()) + array(
                'paper' => sprintf('anh_content_%s_paper', $section),
                'category' => sprintf('anh_content_%s_category', $section),
                'papers' => sprintf('anh_content_%s_papers', $section),
                'categories' => sprintf('anh_content_%s_categories', $section),
            );

            if ($flags['category']) {
                $options['show_categories'] = true;
            }

            if ($flags['tags']) {
                $options['show_tags'] = true;
            }
        }

        $container->setParameter('anh_content.feeds', $config['feeds']);
        $container->setParameter('anh_content.sections', $config['sections']);
        $container->setParameter('anh_content.options', $options);
        $container->setParameter('anh_content.assets_dir', $config['assets_dir']);
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('assetic', array(
            'assets' => array(
                'anh_content_editor_css' => array(
                    'inputs' => array(
                        'bundles/anhcontent/components/codemirror/lib/codemirror.css',
                        'bundles/anhcontent/components/fine-uploader/fineuploader.min.css',
                        '@AnhContentBundle/Resources/assets/css/editor.css',
                    ),
                    'filters' => array(
                        'cssrewrite'
                    )
                ),
                'anh_content_editor_js' => array(
                    'inputs' => array(
                        'bundles/anhcontent/components/codemirror/lib/codemirror.js',
                        'bundles/anhcontent/components/fine-uploader/jquery.fineuploader.min.js',
                        '@AnhContentBundle/Resources/assets/js/editor-toolbar.js',
                        '@AnhContentBundle/Resources/assets/js/editor-tags.js',
                        '@AnhContentBundle/Resources/assets/js/editor.js',
                    )
                )
            ),
            'bundles' => array(
                'AnhContentBundle'
            )
        ));

        $container->prependExtensionConfig('sp_bower', array(
            'assetic' => array(
                'enabled' => false
            ),
            'bundles' => array(
                'AnhContentBundle' => null
            )
        ));

        $container->prependExtensionConfig('stof_doctrine_extensions', array(
            'orm' => array(
                'default' => array(
                    'sluggable' => true,
                    'timestampable' => true
                )
            )
        ));

        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->prependExtensionConfig('oneup_uploader', array(
            'mappings' => array(
                'anh_content_assets' => array(
                    'frontend' => 'fineuploader',
                    'storage' => array(
                        'service' => 'anh_content.asset.storage'
                    ),
                    'allowed_mimetypes' => $config['assets_mime_types'],
                    'max_size' => $config['assets_max_size'],
                )
            )
        ));

        $container->prependExtensionConfig('liip_imagine', array(
            'filter_sets' => array(
                'anh_content_assets_thumb' => array(
                    // 'quality' => 90,
                    // 'format' => 'jpeg',
                    'data_loader' => 'anh_content_asset_data_loader',
                    'filters' => array(
                        'upscale' => array(
                            'min' => array(100, 100)
                        ),
                        'thumbnail' => array(
                            'size' => array(100, 100),
                            'mode' => 'outbound',
                        )
                    )
                )
            )
        ));

        $container->prependExtensionConfig('anh_doctrine_resource', array(
            'resources' => array(
                'anh_content.paper' => array(
                    'model' => '%anh_content.entity.paper.class%',
                    'driver' => 'orm',
                    'controller' => 'Anh\ContentBundle\Controller\PaperController',
                    'rules' => array(
                        'isPublished' => array(
                            'isDraft' => false,
                            'r.publishedSince <= current_timestamp()',
                        ),
                    ),
                ),
                'anh_content.category' => array(
                    'model' => '%anh_content.entity.category.class%',
                    'driver' => 'orm',
                    'controller' => 'Anh\ContentBundle\Controller\CategoryController',
                ),
            )
        ));
    }
}
