<?php

namespace Anh\ContentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AnhContentBundle extends Bundle
{
    const VERSION = 'v1.0.1';
    const TITLE = 'AnhContentBundle';
    const DESCRIPTION = 'Bundle for content management';

    public static function getRequiredBundles()
    {
        return array(
            'Anh\AdminBundle\AnhAdminBundle',
            'Anh\MarkupBundle\AnhMarkupBundle',
            'Anh\TaggableBundle\AnhTaggableBundle',
            'Anh\DateTimePickerBundle\AnhDateTimePickerBundle',
            'Anh\PagerBundle\AnhPagerBundle',
            'Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle',
            'Oneup\UploaderBundle\OneupUploaderBundle',
            'Liip\ImagineBundle\LiipImagineBundle',
        );
    }
}
