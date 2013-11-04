<?php

namespace Anh\ContentBundle\Decoda\Hook;

use Decoda\Hook\AbstractHook;

class PreviewHook extends AbstractHook
{
    /**
     * Leave only preview.
     *
     * @param  string $content
     * @return string
     */
    public function beforeParse($content)
    {
        if ($this->getConfig('previewOnly')) {
            if (preg_match('|\[preview\].+\[\/preview\]|is', $content, $matches)) {
                $content = $matches[0];
            }
        }

        return $content;
    }
}
