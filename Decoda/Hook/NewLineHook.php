<?php

namespace Anh\ContentBundle\Decoda\Hook;

use Decoda\Hook\AbstractHook;

class NewLineHook extends AbstractHook
{
    /**
     * Leaves only 2 line break
     *
     * @param  string $content
     * @return string
     */
    public function beforeParse($content)
    {
        return preg_replace("/[\n|\r|\n\r|\r\n]{2,}/", "\n\n", $content);
    }
}
