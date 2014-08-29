<?php

namespace Anh\ContentBundle\Controller;

use Anh\DoctrineResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Anh\ContentBundle\Entity\Category;

class PaperController extends ResourceController
{
    use InjectOptionsTrait;

    /**
     * Lists all papers in section
     */
    public function listPapersAction(Request $request, $section, $page = null, $limit = null)
    {
        $options = array(
            'view' => 'AnhContentBundle:Default:_listPapers.html.twig',
            'criteria' => array(
                'section' => $section,
                '[isPublished]',
            ),
            'data' => array(
                'section' => $section,
            ),
        );

        if (!is_null($page)) {
            $options['page'] = $page;
            $options['view'] = 'AnhContentBundle:Default:_paginatePapers.html.twig';
        }

        if (!is_null($limit)) {
            $options['limit'] = $limit;
        }

        $this->injectOptions($request, $options);

        return $this->listAction($request);
    }

    /**
     * View paper
     */
    public function viewPaperAction(Request $request, $section, $slug)
    {
        $this->injectOptions($request, array(
            'view' => 'AnhContentBundle:Default:_viewPaper.html.twig',
            'criteria' => array(
                'section' => $section,
                'slug' => $slug,
            ),
            'data' => array(
                'section' => $section,
            ),
        ));

        return $this->showAction($request);
    }

    /**
     * Lists papers in category.
     */
    public function viewCategoryAction(Request $request, $section, Category $category, $page = null, $limit = null)
    {
        $sections = $this->container->getParameter('anh_content.sections');

        if (!$sections[$section]['category']) {
            throw new \InvalidArgumentException(
                sprintf("Categories not enabled for section '%s'.", $section)
            );
        }

        $options = array(
            'view' => 'AnhContentBundle:Default:_viewCategory.html.twig',
            'criteria' => array(
                'section' => $section,
                'category' => $category,
                '[isPublished]',
            ),
            'data' => array(
                'section' => $section,
                'category' => $category,
            ),
        );

        if (!is_null($page)) {
            $options['page'] = $page;
            $options['view'] = 'AnhContentBundle:Default:_paginateCategory.html.twig';
        }

        if (!is_null($limit)) {
            $options['limit'] = $limit;
        }

        $this->injectOptions($request, $options);

        return $this->listAction($request);
    }

    public function createAction(Request $request)
    {
        return $this->handleResponse(parent::createAction($request));
    }

    public function updateAction(Request $request)
    {
        return $this->handleResponse(parent::updateAction($request));
    }

    protected function handleResponse($response)
    {
        if (isset($response['redirect'])) {
            if ($response['data']['resource_form']->get('save_and_preview')->isClicked()) {
                $response['redirect'] = $this->container->get('anh_content.url_generator')->resolveAndGenerate(
                    $response['data']['resource']
                );
            }
        }

        return $response;
    }
}
