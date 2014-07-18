<?php

namespace Anh\ContentBundle\Controller;

use Anh\DoctrineResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends ResourceController
{
    use InjectOptionsTrait;

    /**
     * Lists all categories in section
     */
    public function listCategoriesAction(Request $request, $section, $page = null, $limit = null)
    {
        $sections = $this->container->getParameter('anh_content.sections');

        if (!$sections[$section]['category']) {
            throw new \InvalidArgumentException(
                sprintf("Categories not enabled for section '%s'.", $section)
            );
        }

        $options = array(
            'view' => 'AnhContentBundle:Default:_listCategories.html.twig',
            'criteria' => array(
                'section' => $section,
            ),
            'viewVars' => array(
                'section' => $section,
            ),
        );

        if (!is_null($page)) {
            $options['page'] = $page;
            $options['view'] = 'AnhContentBundle:Default:_paginateCategories.html.twig';
        }

        if (!is_null($limit)) {
            $options['limit'] = $limit;
        }

        $this->injectOptions($request, $options);

        return $this->listAction($request);
    }
}
