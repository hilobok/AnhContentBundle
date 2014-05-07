<?php

namespace Anh\ContentBundle;

use Oneup\UploaderBundle\Uploader\Storage\FilesystemStorage;
use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AssetUploaderStorage extends FilesystemStorage
{
    /**
     * {@inheritdoc}
     *
     * Saves original file name.
     */
    public function upload(FileInterface $file, $name, $path = null)
    {
        $originalFileName = ($file instanceof UploadedFile) ?
            $file->getClientOriginalName() : ''
        ;

        $uploaded = parent::upload($file, $name, $path);
        $uploaded->originalFileName = $originalFileName;

        return $uploaded;
    }
}
