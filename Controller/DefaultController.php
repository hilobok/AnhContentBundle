<?php

namespace Anh\ContentBundle\Controller;

class DefaultController extends BaseController
{
    /**
     * Lists all papers in section
     *
     * @param string $section
     *
     * @return Response
     */
    public function listPapersAction($section)
    {
        return $this->render('AnhContentBundle:Default:listPapers.html.twig', array(
            'section' => $section,
            'papers' => $this->getPublishedPapers($section)
        ));
    }

    /**
     * Paginates papers in section
     *
     * @param string $section
     * @param integer $page
     * @param integer $limit
     *
     * @return Response
     */
    public function paginatePapersAction($section, $page = 1, $limit = 10)
    {
        $pager = $this->paginatePublishedPapers($section, $page, $limit)
            ->setUrl(str_replace('0', '{page}',
                $this->contentUrl('papers', $section, array(
                    'page' => 0
                ))
            ))
        ;

        return $this->render('AnhContentBundle:Default:paginatePapers.html.twig', array(
            'section' => $section,
            'pager' => $pager
        ));
    }

    /**
     * Lists all categories in section
     *
     * @param string $section
     *
     * @return Response
     */
    public function listCategoriesAction($section)
    {
        $sections = $this->getSections();

        if (!$sections[$section]['category']) {
            throw new \InvalidArgumentException(
                sprintf("Categories not enabled for section '%s'.", $section)
            );
        }

        return $this->render('AnhContentBundle:Default:listCategories.html.twig', array(
            'section' => $section,
            'categories' => $this->getCategories($section)
        ));
    }

    /**
     * Paginates categories in section
     *
     * @param string $section
     * @param integer $page
     * @param integer $limit
     *
     * @return Response
     */
    public function paginateCategoriesAction($section, $page = 1, $limit = 10)
    {
        $sections = $this->getSections();

        if (!$sections[$section]['category']) {
            throw new \InvalidArgumentException(
                sprintf("Categories not enabled for section '%s'.", $section)
            );
        }

        $pager = $this->paginateCategories($section, $page, $limit)
            ->setUrl(str_replace('0', '{page}',
                $this->contentUrl('categories', $section, array(
                    'page' => 0
                ))
            ))
        ;

        return $this->render('AnhContentBundle:Default:paginateCategories.html.twig', array(
            'section' => $section,
            'pager' => $pager
        ));
    }

    /**
     * View paper
     *
     * @param string $section
     * @param string $slug
     *
     * @return Response
     */
    public function viewPaperAction($section, $slug)
    {
        return $this->render('AnhContentBundle:Default:viewPaper.html.twig', array(
            'section' => $section,
            'paper' => $this->getPaper($section, $slug)
        ));
    }

    /**
     * Lists papers in category
     *
     * @param string $section
     * @param string $slug
     *
     * @return Response
     */
    public function viewCategoryAction($section, $slug)
    {
        $sections = $this->getSections();

        if (!$sections[$section]['category']) {
            throw new \InvalidArgumentException(
                sprintf("Categories not enabled for section '%s'.", $section)
            );
        }

        $category = $this->getCategory($section, $slug);
        $papers = $this->getPublishedPapersInCategory($section, $category);

        return $this->render('AnhContentBundle:Default:viewCategory.html.twig', array(
            'section' => $section,
            'category' => $category,
            'papers' => $papers
        ));
    }

    /**
     * Paginates papers in category
     *
     * @param string $section
     * @param integer $page
     * @param integer $limit
     *
     * @return Response
     */
    public function paginateCategoryAction($section, $slug, $page = 1, $limit = 10)
    {
        $sections = $this->getSections();

        if (!$sections[$section]['category']) {
            throw new \InvalidArgumentException(
                sprintf("Categories not enabled for section '%s'.", $section)
            );
        }

        $category = $this->getCategory($section, $slug);
        $pager = $this->paginatePublishedPapersInCategory($section, $category, $page, $limit)
            ->setUrl(str_replace('0', '{page}',
                $this->contentUrl('category', $section, $category->getUrlParameters() + array(
                    'page' => 0
                ))
            ))
        ;

        return $this->render('AnhContentBundle:Default:paginateCategory.html.twig', array(
            'section' => $section,
            'category' => $category,
            'pager' => $pager
        ));
    }
}
