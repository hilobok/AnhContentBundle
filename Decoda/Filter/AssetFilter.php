<?php

namespace Anh\ContentBundle\Decoda\Filter;

use Decoda\Decoda;
use Decoda\Filter\AbstractFilter;
use Anh\ContentBundle\AssetManager;

class AssetFilter extends AbstractFilter
{
    const ALIGN = '/^(left|right|center|none)$/';

    /**
     * Supported tags.
     *
     * @var array
     */
    protected $_tags = array(
        'asset' => array(
            'template' => 'asset',
            'autoClose' => true,
            'displayType' => Decoda::TYPE_BLOCK,
            'allowedTypes' => Decoda::TYPE_NONE,
            'attributes' => array(
                'default' => self::WILDCARD,
                'title' => self::WILDCARD,
                'align' => self::ALIGN,
                'alt' => self::WILDCARD,
                'filter' => self::WILDCARD
            )
        )
    );

    /**
     * Prepare attributes for render.
     *
     * @param  array  $tag
     * @param  string $content
     * @return array
     */
    public function asset(array $tag, $content)
    {
        $asset = $tag['attributes']['default'];

        $filter = isset($tag['attributes']['filter']) ?
            $tag['attributes']['filter'] : $this->getConfig('filter')
        ;

        $isImage = $this->isImage($asset);

        if (!$isImage) {
            $filter = '';
        }

        $tag['attributes']['isImage'] = $isImage;

        $assetManager = $this->getConfig('assetManager');

        $tag['attributes']['src'] = $assetManager->getUrl($asset, $filter);

        if (empty($tag['attributes']['align'])) {
            $tag['attributes']['align'] = 'center';
        }

        if (empty($tag['attributes']['alt'])) {
            $tag['attributes']['alt'] = empty($tag['attributes']['title']) ? '' : $tag['attributes']['title'];
        }

        return array($tag, $content);
    }

    protected function isImage($asset)
    {
        return in_array(
            strtolower(pathinfo($asset, PATHINFO_EXTENSION)),
            array('jpg', 'jpeg', 'png', 'gif', 'bmp'),
            true
        );
    }
}
