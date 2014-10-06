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
    public function afterParse($content)
    {
        if ($this->getConfig('previewOnly')) {
            if (preg_match('/{##preview##}.*?{##preview##}/s', $content, $matches)) {
                $content = $matches[0];
            }
        }

        return str_replace('{##preview##}', '', $content);
    }
}
