<?php

namespace Anh\Bundle\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function documentListAction($section)
    {
        $documents = $this->container->get('anh_content.manager.document')
            ->findPublishedInSection($section)
        ;

        return $this->render('AnhContentBundle:Default:list.html.twig', array(
            'section' => $section,
            'documents' => $documents
        ));
    }

    public function documentViewAction($section, $slug)
    {
        $document = $this->container->get('anh_content.manager.document')
            ->findInSectionBySlug($section, $slug)
        ;

        return $this->render('AnhContentBundle:Default:view.html.twig', array(
            'section' => $section,
            'document' => $document
        ));
    }

    public function categoryViewAction($section, $slug)
    {
        $category = $this->container->get('anh_content.manager.category')
            ->findInSectionBySlug($section, $slug)
        ;

        $documents = $this->container->get('anh_content.manager.document')
            ->findPublishedInSectionAndCategory($section, $category)
        ;

        return $this->render('AnhContentBundle:Default:category.html.twig', array(
            'section' => $section,
            'category' => $category,
            'documents' => $documents
        ));
    }
}
