<?php

namespace Anh\ContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;

class FeedsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $feeds = $container->getParameter('anh_content.feeds');

        foreach ($feeds as $feed => $value) {
            $definition = new Definition('Anh\ContentBundle\FeedDataProvider');

            $definition->addArgument(new Reference('anh_content.manager.paper'));
            $definition->addArgument(new Reference('anh_content.manager.category'));
            $definition->addArgument(new Reference('anh_content.url_generator'));
            $definition->addArgument(new Reference('anh_feed.url_generator'));

            $definition->addTag('anh_feed.data_provider', array('feed' => $feed));

            $definition->addMethodCall('setOptions', array($value['options']));
            $definition->addMethodCall('setConditions', array($value['conditions']));

            $container->setDefinition(
                sprintf('anh_content.feed_%s', $feed),
                $definition
            );
        }
    }
}
