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
            $flags['routes'] = (empty($falgs['routes']) ? array() : $flags['routes']) + array(
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
                        'bundles/anhcontent/editor.css'
                    ),
                    'filters' => array(
                        'cssrewrite'
                    )
                ),
                'anh_content_editor_js' => array(
                    'inputs' => array(
                        'bundles/anhcontent/components/codemirror/lib/codemirror.js',
                        'bundles/anhcontent/components/fine-uploader/jquery.fineuploader.min.js',
                        'bundles/anhcontent/editor-bbcode-tagset.js',
                        'bundles/anhcontent/editor.js'
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

        $container->prependExtensionConfig('oneup_uploader', array(
            'mappings' => array(
                'anh_content_assets' => array(
                    'frontend' => 'fineuploader',
                    'storage' => array(
                        'service' => 'anh_content.asset.storage'
                    ),
                    'allowed_extensions' => array(
                        'jpg', 'JPG',
                        'jpeg', 'JPEG',
                        'png', 'PNG',
                        'gif', 'GIF'
                    ),
                    // 'max_size' => '20k',
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
    }
}
