<?php

namespace Anh\ContentBundle;

use Symfony\Component\HttpFoundation\File\File;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class AssetManager
{
    /**
     * Holds liip_imagine cache manager.
     *
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * Holds filters for cache invalidation.
     *
     * @var array
     */
    protected $filters;

    /**
     * Holds absolute path to directory with assets.
     *
     * @var string
     */
    protected $assetsDir;

    public function __construct($assetsDir, CacheManager $cacheManager, array $filters)
    {
        $this->assetsDir = $assetsDir;
        $this->cacheManager = $cacheManager;

        // we will invalidate cache only for filters with our data loader, so collect them
        foreach ($filters as $filter => $options) {
            if ($options['data_loader'] == 'anh_content_asset_data_loader') {
                $this->filters[] = $filter;
            }
        }
    }

    /**
     * Creates asset from file
     *
     * @param File $file
     *
     * @return array
     */
    public function create(File $file)
    {
        $asset = $file->getFilename();

        return array(
            'fileName' => $asset,
            'thumb' => $this->getUrl($asset, 'anh_content_assets_thumb'),
            'url' => $this->getUrl($asset),
            'size' => $file->getSize(),
            'originalFileName' => empty($file->originalFileName) ?
                $file->getFilename() :
                $file->originalFileName
        );
    }

    /**
     * Gets asset url original or filtered through liip_imagine
     *
     * @param string $asset
     * @param string $filter
     *
     * @return string
     */
    public function getUrl($asset, $filter = '')
    {
        if (empty($asset)) {
            return 'not-existent-path';
        }

        return empty($filter) ?
            $this->getUrlToOriginal($asset) :
            $this->getUrlToFiltered($asset, $filter)
        ;
    }

    /**
     * Gets url to original uploaded file
     *
     * @param string $asset
     *
     * @return string
     */
    protected function getUrlToOriginal($asset)
    {
        $webRoot = realpath($this->cacheManager->getWebRoot());
        $assetsDir = realpath($this->assetsDir);

        if (strpos($assetsDir, $webRoot) !== 0) {
            throw new \RuntimeException(sprintf("Unable to get url. Assets directory '%s' not in web root.", $assetsDir));
        }

        $relativePath = substr($assetsDir, strlen($webRoot));

        return sprintf('%s/%s', $relativePath, $asset);
    }

    /**
     * Gets url to filtered and cached file via liip_imagine
     */
    protected function getUrlToFiltered($asset, $filter, $absolute = false)
    {
        return $this->cacheManager->getBrowserPath($asset, $filter, $absolute);
    }

    /**
     * Removes original file and invalidate cache
     *
     * @param string $asset
     */
    public function remove($asset)
    {
        $file = $this->assetsDir . '/' . $asset;

        // remove from assets dir
        if (is_file($file)) {
            unlink($file);
        }

        // invalidate cache
        foreach ($this->filters as $filter) {
            $this->cacheManager->remove($asset, $filter);
        }
    }
}
