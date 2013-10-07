<?php

namespace Anh\Bundle\ContentBundle;

class AssetManager
{
    private $rootDir;

    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir . '/../web';
    }

    public function getPath($to, $absolute = false)
    {
        switch ($to) {
            case 'uploads':
                $path = '/media/uploads/anh_content_assets/';
                break;

            case 'thumbs':
                $path = '/media/cache/anh_content_assets/media/uploads/anh_content_assets/';
                break;

            default:
                throw new \InvalidArgumentException(sprintf("Unable to get path for '%s'.", $to));
        }

        return $absolute ? $this->rootDir . $path : $path;
    }

    public function delete($fileName)
    {
        foreach (array('uploads', 'thumbs') as $where) {
            $file = $this->getPath($where, true) . $fileName;
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
