<?php

namespace Anh\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Anh\ContentBundle\Entity\Category;

class BaseController extends Controller
{
    /**
     * Returns configured sections
     *
     * @return array
     */
    protected function getSections()
    {
        return $this->container->getParameter('anh_content.sections');
    }

    /**
     * Generates url for content
     *
     * @param string $alias Route alias defined in section config
     * @param string $section Section name
     * @param array $parameters Additional route parameters
     */
    protected function contentUrl($alias, $section, array $parameters = array())
    {
        return $this->container->get('anh_content.url_generator')
            ->generateUrl($alias, $section, $parameters)
        ;
    }

    /**
     * Returns paper with given slug
     *
     * @param string $section
     * @param string $slug
     *
     * @return \Anh\ContentBundle\Entity\Paper
     */
    protected function getPaper($section, $slug)
    {
        return $this->container->get('anh_content.manager.paper')
            ->findInSectionBySlug($section, $slug)
        ;
    }

    /**
     * Returns category with given slug
     *
     * @param string $section Section name
     * @param string $slug
     *
     * @return \Anh\ContentBundle\Entity\Category
     */
    protected function getCategory($section, $slug)
    {
        return $this->container->get('anh_content.manager.category')
            ->findInSectionBySlug($section, $slug)
        ;
    }

    /**
     * Returns categories list in section
     *
     * @param string $section Section name
     *
     * @return \Anh\ContentBundle\Entity\Category[]
     */
    protected function getCategories($section)
    {
        return $this->container->get('anh_content.manager.category')
            ->findInSection($section)
        ;
    }

    /**
     * Paginates categories list in section
     *
     * @param string $section Section name
     *
     * @return \Anh\PagerBundle\Pager
     */
    protected function paginateCategories($section)
    {
        return $this->container->get('anh_content.manager.category')
            ->paginateInSection($section)
        ;
    }

    /**
     * Returns list of published papers in section
     *
     * @param string $section Section name
     *
     * @return \Anh\ContentBundle\Entity\Paper[]
     */
    protected function getPublishedPapers($section)
    {
        return $this->container->get('anh_content.manager.paper')
            ->findPublishedInSection($section)
        ;
    }

    /**
     * Paginates list of published papers in section
     *
     * @param string $section Section name
     * @param \Anh\ContentBundle\Entity\Category $category
     * @param integer $page Number of page to fetch
     * @param integer $limit Rows per page
     *
     * @return \Anh\PagerBundle\Pager
     */
    protected function paginatePublishedPapers($section, $page = 1, $limit = 10)
    {
        return $this->container->get('anh_content.manager.paper')
            ->paginatePublishedInSection($section, $page, $limit)
        ;
    }

    /**
     * Returns list of published papers in category
     *
     * @param string $section Section name
     * @param \Anh\ContentBundle\Entity\Category $category
     *
     * @return \Anh\ContentBundle\Entity\Paper[]
     */
    protected function getPublishedPapersInCategory($section, Category $category)
    {
        return $this->container->get('anh_content.manager.paper')
            ->findPublishedInSectionAndCategory($section, $category)
        ;
    }

    /**
     * Paginates list of published papers in category
     *
     * @param string $section Section name
     * @param \Anh\ContentBundle\Entity\Category $category
     * @param integer $page Number of page to fetch
     * @param integer $limit Rows per page
     *
     * @return \Anh\PagerBundle\Pager
     */
    protected function paginatePublishedPapersInCategory($section, Category $category, $page = 1, $limit = 10)
    {
        return $this->container->get('anh_content.manager.paper')
            ->paginatePublishedInSectionAndCategory($section, $category, $page, $limit)
        ;
    }

    /**
     * Returns list of published papers with image
     *
     * @param string $section Section name
     *
     * @return \Anh\ContentBundle\Entity\Paper[]
     */
    protected function getPublishedWithImageInSection($section)
    {
        return $this->container->get('anh_content.manager.paper')
            ->findPublishedWithImageInSection($section)
        ;
    }

    /**
     * Paginates list of published papers with image
     *
     * @param string $section Section name
     * @param integer $page Number of page to fetch
     * @param integer $limit Rows per page
     *
     * @return \Anh\PagerBundle\Pager
     */
    protected function paginatePublishedWithImageInSection($section, $page = 1, $limit = 10)
    {
        return $this->container->get('anh_content.manager.paper')
            ->paginatePublishedWithImageInSection($section, $page, $limit)
        ;
    }
}