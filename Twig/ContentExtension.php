<?php

namespace Anh\ContentBundle\Twig;

use Anh\ContentBundle\UrlGenerator;
use Anh\ContentBundle\AssetManager;
use Anh\ContentBundle\Entity\Paper;
use Anh\ContentBundle\Entity\Category;

class ContentExtension extends \Twig_Extension
{
    /**
     * Holds url generator service
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * Holds asset manager
     * @var AssetManager
     */
    protected $assetManager;

    protected $sections;

    /**
     * Constructor
     */
    public function __construct(UrlGenerator $urlGenerator, AssetManager $assetManager, $sections)
    {
        $this->urlGenerator = $urlGenerator;
        $this->assetManager = $assetManager;
        $this->sections = $sections;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('assetUrl', array($this, 'assetUrl')),
            new \Twig_SimpleFunction('contentUrl', array($this, 'contentUrl')),
            new \Twig_SimpleFunction('contentSections', array($this, 'contentSections'))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'anh_content';
    }

    /**
     * Returns url for asset
     *
     * @param string $asset
     * @param string $filter
     *
     * @return string
     */
    public function assetUrl($asset, $filter = '')
    {
        return $this->assetManager->getUrl($asset, $filter);
    }

    /**
     * Returns url for content
     *
     * @param mixed $content Could be either paper or category entity or section
     * @param string|null $name
     *
     * @return string
     */
    public function contentUrl($data, $absolute = false)
    {
        return $this->urlGenerator->resolveAndGenerate($data, $absolute);
    }

    /**
     * Returns config for section or all sections if $section ommited
     *
     * @param string|null $section Section name
     *
     * @return array
     */
    public function contentSections($section = null)
    {
        if (isset($section) and !isset($this->sections[$section])) {
            throw new \InvalidArgumentException(sprintf("Section '%s' not exists.", $section));
        }

        return isset($section) ? $this->sections[$section] : $this->sections;
    }
}