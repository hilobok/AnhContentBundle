<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/decoda/blob/master/license.md
 * @link        http://milesj.me/code/php/decoda
 */

namespace Anh\ContentBundle\Decoda\Filter;

use Decoda\Decoda;
use Decoda\Filter\AbstractFilter;

/**
 * Provides tags for URLs.
 */
class UrlFilter extends AbstractFilter
{
    /**
     * Configuration.
     *
     * @type array
     */
    protected $_config = array(
        'protocols' => array('http', 'ftp', 'irc', 'telnet')
    );

    /**
     * Supported tags.
     *
     * @type array
     */
    protected $_tags = array(
        'url' => array(
            'htmlTag' => 'a',
            'displayType' => Decoda::TYPE_INLINE,
            'allowedTypes' => Decoda::TYPE_INLINE,
            'attributes' => array(
                'default' => true,
                'title' => self::WILDCARD
            ),
            'mapAttributes' => array(
                'default' => 'href'
            )
        ),
        'link' => array(
            'htmlTag' => 'a',
            'displayType' => Decoda::TYPE_INLINE,
            'allowedTypes' => Decoda::TYPE_INLINE,
            'attributes' => array(
                'default' => true,
                'title' => self::WILDCARD
            ),
            'mapAttributes' => array(
                'default' => 'href'
            )
        )
    );

    /**
     * Strip a node but keep the URL regardless of location.
     *
     * @param  array  $tag
     * @param  string $content
     * @return string
     */
    public function strip(array $tag, $content)
    {
        $url = isset($tag['attributes']['href']) ? $tag['attributes']['href'] : $content;

        return parent::strip($tag, $url);
    }
}
