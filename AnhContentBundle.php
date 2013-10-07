<?php

namespace Anh\Bundle\ContentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AnhContentBundle extends Bundle
{
    const VERSION = '1.0.0-dev';
    const TITLE = 'AnhContentBundle';
    const DESCRIPTION = 'Bundle for content management';

    public static function getRequiredBundles()
    {
        return array(
            'Anh\Bundle\AdminBundle\AnhAdminBundle',
            'Anh\Bundle\MarkupBundle\AnhMarkupBundle',
            'Anh\Bundle\PagerBundle\AnhPagerBundle',
            'Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle',
            'Oneup\UploaderBundle\OneupUploaderBundle',
            'Liip\ImagineBundle\LiipImagineBundle',
        );
    }
}
