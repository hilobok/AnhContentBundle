<?php

namespace Anh\ContentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Anh\ContentBundle\DependencyInjection\Compiler\FeedsCompilerPass;

class AnhContentBundle extends Bundle
{
    const VERSION = 'v2.4.1';
    const TITLE = 'AnhContentBundle';
    const DESCRIPTION = 'Bundle for content management';

    public static function getRequiredBundles()
    {
        return array(
            'Anh\AdminBundle\AnhAdminBundle',
            'Anh\MarkupBundle\AnhMarkupBundle',
            'Anh\TaggableBundle\AnhTaggableBundle',
            'Anh\DoctrineResourceBundle\AnhDoctrineResourceBundle',
            'Anh\DateTimePickerBundle\AnhDateTimePickerBundle',
            'Anh\PaginatorBundle\AnhPaginatorBundle',
            'Anh\FeedBundle\AnhFeedBundle',
            'Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle',
            'Oneup\UploaderBundle\OneupUploaderBundle',
            'Liip\ImagineBundle\LiipImagineBundle',
            'Sp\BowerBundle\SpBowerBundle',
        );
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FeedsCompilerPass());
    }
}
