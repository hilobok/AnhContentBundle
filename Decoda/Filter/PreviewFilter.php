<?php

namespace Anh\Bundle\ContentBundle\Decoda\Filter;

use Decoda\Decoda;
use Decoda\Filter\AbstractFilter;
use Anh\Bundle\ContentBundle\Decoda\Hook\PreviewHook;

class PreviewFilter extends AbstractFilter
{
    /**
     * Supported tags.
     *
     * @var array
     */
    protected $_tags = array(
        'preview' => array(
            'displayType' => Decoda::TYPE_BLOCK,
            'allowedTypes' => Decoda::TYPE_BOTH,
        ),

        'proceed' => array(
            'htmlTag' => 'a',
            'displayType' => Decoda::TYPE_INLINE,
            'allowedTypes' => Decoda::TYPE_INLINE,
        )
    );

    public function parse(array $tag, $content)
    {
        if ($tag['tag'] == 'preview') {
            return $content;
        }

        if ($tag['tag'] == 'proceed') {
            if (!$this->getConfig('previewOnly')) {
                return $content;
            }

            $tag['attributes']['href'] = $this->getConfig('url');
        }

        return parent::parse($tag, $content);
    }

    /**
     * Add preview hook.
     *
     * @param  \Decoda\Decoda                                        $decoda
     * @return \Anh\Bundle\ContentBundle\Decoda\Filter\PreviewFilter
     */
    public function setupHooks(Decoda $decoda)
    {
        $decoda->addHook(new PreviewHook($this->_config));

        return $this;
    }
}
