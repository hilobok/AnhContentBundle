<?php

namespace Anh\Bundle\ContentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

use Anh\Bundle\ContentBundle\AssetManager;

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
            if (empty($flags['route'])) {
                $flags['route'] = sprintf('anh_content_%s_view', $section);
            }

            if ($flags['category']) {
                $options['show_categories'] = true;
            }

            if ($flags['tags']) {
                $options['show_tags'] = true;
            }
        }

        $container->setParameter('anh_content.sections', $config['sections']);
        $container->setParameter('anh_content.options', $options);
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('assetic', array(
            'bundles' => array(
                'AnhContentBundle'
            )
        ));

        $config = array(
            'orm' => array(
                'default' => array(
                    'sluggable' => true,
                    'timestampable' => true
                )
            )
        );
        $container->prependExtensionConfig('stof_doctrine_extensions', $config);

        $configs = $container->getExtensionConfig($this->getAlias());
        $this->load($configs, $container);
        $assetManager = $container->get('anh_content.asset_manager');

        $config = array(
            'mappings' => array(
                'anh_content_assets' => array(
                    'frontend' => 'fineuploader',
                    'storage' => array(
                        'directory' => '.' . $assetManager->getPath('uploads')
                    ),
                    'allowed_extensions' => array('jpg', 'jpeg', 'png', 'gif'),
                    // 'max_size' => '20k',
                    // 'error_handler' => 'anh_content.uploader_error_handler'
                )
            )
        );
        $container->prependExtensionConfig('oneup_uploader', $config);

        $config = array(
            'filter_sets' => array(
                'anh_content_assets_thumb' => array( // assets thumbs
                    'quality' => 90,
                    'format' => 'jpeg',
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
        );
        $container->prependExtensionConfig('liip_imagine', $config);
    }
}
