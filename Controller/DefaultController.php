<?php

namespace Anh\Bundle\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function paperListAction($section)
    {
        $papers = $this->container->get('anh_content.manager.paper')
            ->findPublishedInSection($section)
        ;

        return $this->render('AnhContentBundle:Default:list.html.twig', array(
            'section' => $section,
            'papers' => $papers
        ));
    }

    public function paperViewAction($section, $slug)
    {
        $paper = $this->container->get('anh_content.manager.paper')
            ->findInSectionBySlug($section, $slug)
        ;

        return $this->render('AnhContentBundle:Default:view.html.twig', array(
            'section' => $section,
            'paper' => $paper
        ));
    }

    public function categoryViewAction($section, $slug)
    {
        $category = $this->container->get('anh_content.manager.category')
            ->findInSectionBySlug($section, $slug)
        ;

        $papers = $this->container->get('anh_content.manager.paper')
            ->findPublishedInSectionAndCategory($section, $category)
        ;

        return $this->render('AnhContentBundle:Default:category.html.twig', array(
            'section' => $section,
            'category' => $category,
            'papers' => $papers
        ));
    }
}
