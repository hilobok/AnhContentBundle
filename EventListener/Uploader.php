<?php

namespace Anh\Bundle\ContentBundle\EventListener;

use Oneup\UploaderBundle\Event\PostPersistEvent;

class Uploader
{
    public function onUpload(PostPersistEvent $event)
    {
        $file = $event->getFile();
        $response = $event->getResponse();

        if ($file) {
            $response['fileName'] = $file->getFilename();
            $response['size'] = $file->getSize();
        }
    }
}
