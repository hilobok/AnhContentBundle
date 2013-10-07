<?php

namespace Anh\Bundle\ContentBundle\Decoda\Filter;

use Decoda\Decoda;
use Decoda\Filter\AbstractFilter;

class AssetFilter extends AbstractFilter
{
    const ASSET = '/^(.+?)\.(jpg|jpeg|png|gif|bmp)$/i';
    const ALIGN = '/^(left|right|center|inline)$/';

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
                'default' => self::ASSET,
                'title' => self::WILDCARD,
                'align' => self::ALIGN,
                'alt' => self::WILDCARD
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
        $tag['attributes']['src'] = $this->getConfig('path') . $tag['attributes']['default'];

        if (empty($tag['attributes']['align'])) {
            $tag['attributes']['align'] = 'center';
        }

        if (empty($tag['attributes']['alt'])) {
            $tag['attributes']['alt'] = empty($tag['attributes']['title']) ? '' : $tag['attributes']['title'];
        }

        return array($tag, $content);
    }
}
