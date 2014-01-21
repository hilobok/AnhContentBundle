<?php

namespace Anh\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Anh\ContentBundle\Entity\Category;
use Anh\ContentBundle\Entity\Paper;
use Anh\Taggable\Entity\Tag;

use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    public function indexAction()
    {
        $options = $this->container->getParameter('anh_content.options');
        $sections = $this->container->getParameter('anh_content.sections');

        return $this->render('AnhContentBundle:Admin:index.html.twig', array(
            'sections' => $sections,
            'options' => $options
        ));
    }

    public function tagListAction($page = 1)
    {
        $pager = $this->getPaperManager()->paginateTags($page, 10)
            ->setUrl(str_replace('0', '{page}',
                $this->generateUrl('anh_content_admin_tag_list', array(
                    'page' => 0
                ))
            ))
        ;

        $options = $this->container->getParameter('anh_content.options');
        $sections = $this->container->getParameter('anh_content.sections');

        return $this->render('AnhContentBundle:Admin:tag/list.html.twig', array(
            'sections' => $sections,
            'options' => $options,
            'pager' => $pager
        ));
    }

    public function tagEditAction(Tag $tag)
    {
        $form = $this->createForm('anh_taggable_form_type_tag', $tag);
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $this->getPaperManager()->save($tag);

                return $this->redirect($this->generateUrl('anh_content_admin_tag_list'));
            }
        }

        $options = $this->container->getParameter('anh_content.options');
        $sections = $this->container->getParameter('anh_content.sections');

        return $this->render('AnhContentBundle:Admin:tag/edit.html.twig', array(
            'sections' => $sections,
            'options' => $options,
            'form' => $form->createView()
        ));
    }

    public function tagDeleteAction()
    {
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $list = $request->request->get('id');

            if (!empty($list) and is_array($list)) {
                $this->getPaperManager()
                    ->getTaggableManager()
                    ->deleteTagsByIdList($list)
                ;
            }
        }

        return $this->redirect($this->generateUrl('anh_content_admin_tag_list'));
    }

    public function paperListAction($section, $page = 1)
    {
        $sections = $this->container->getParameter('anh_content.sections');

        if (!in_array($section, array_keys($sections))) {
            throw new \InvalidArgumentException("Section '{$section}' not configured.");
        }

        $options = $this->container->getParameter('anh_content.options');

        $pager = $this->getPaperManager()->paginateInSection($section, $page, 10)
            ->setUrl(str_replace('0', '{page}',
                $this->generateUrl('anh_content_admin_paper_list', array(
                    'section' => $section,
                    'page' => 0
                ))
            ))
        ;

        return $this->render('AnhContentBundle:Admin:paper/list.html.twig', array(
            'sections' => $sections,
            'options' => $options,
            'section' => $section,
            'pager' => $pager
        ));
    }

    public function paperAddAction($section)
    {
        $sections = $this->container->getParameter('anh_content.sections');

        if (!in_array($section, array_keys($sections))) {
            throw new \InvalidArgumentException("Section '{$section}' not configured.");
        }

        $paper = $this->getPaperManager()->create();
        $paper->setSection($section);

        return $this->paperAddEdit($paper, 'AnhContentBundle:Admin:paper/add.html.twig');
    }

    public function paperEditAction($section, Paper $paper)
    {
        $sections = $this->container->getParameter('anh_content.sections');

        if (!in_array($section, array_keys($sections))) {
            throw new \InvalidArgumentException("Section '{$section}' not configured.");
        }

        return $this->paperAddEdit($paper,
            'AnhContentBundle:Admin:paper/edit.html.twig',
            $this->getRequest()->server->get('HTTP_REFERER')
        );
    }

    private function paperAddEdit(Paper $paper, $template, $redirect = null)
    {
        $form = $this->createForm('anh_content_form_type_paper', $paper);
        $form->get('_redirect')->setData($redirect);

        $request = $this->getRequest();
        $section = $paper->getSection();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $this->getPaperManager()->save($paper);

                return $this->redirect($form->get('_redirect')->getData() ?: $this->generateUrl(
                    'anh_content_admin_paper_list',
                    array('section' => $section)
                ));
            }
        }

        $options = $this->container->getParameter('anh_content.options');
        $sections = $this->container->getParameter('anh_content.sections');

        $assetManager = $this->container->get('anh_content.asset.manager');

        // getting all available bbcode tags from parser
        $parser = $this->container->get('anh_markup.parser');
        $decoda = $parser->create('bbcode', '', array());
        $tags = array();
        foreach ($decoda->getFilters() as $filter) {
            $tags = array_merge($tags, array_keys($filter->getTags()));
        }

        // remove not valid bbcode tags
        $tags = array_filter($tags, function($value) { return preg_match('/^[_a-z0-9]+$/', $value); });

        return $this->render($template, array(
            'tags' => $tags,
            'sections' => $sections,
            'options' => $options,
            'section' => $section,
            'form' => $form->createView()
        ));
    }

    public function paperDeleteAction($section)
    {
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $list = $request->request->get('id');

            if (!empty($list) and is_array($list)) {
                $this->getPaperManager()->deleteByIdList($list);
            }
        }

        return $this->redirect($this->generateUrl(
            'anh_content_admin_paper_list',
            array('section' => $section)
        ));
    }

    public function categoryListAction()
    {
        $categories = $this->getCategoryManager()->findAll();

        $options = $this->container->getParameter('anh_content.options');
        $sections = $this->container->getParameter('anh_content.sections');

        return $this->render('AnhContentBundle:Admin:category/list.html.twig', array(
            'sections' => $sections,
            'options' => $options,
            'categories' => $categories
        ));
    }

    public function categoryAddAction()
    {
        $category = $this->getCategoryManager()->create();

        return $this->categoryAddEdit($category, 'AnhContentBundle:Admin:category/add.html.twig');
    }

    public function categoryEditAction(Category $category)
    {
        return $this->categoryAddEdit($category,
            'AnhContentBundle:Admin:category/edit.html.twig'
        );
    }

    private function categoryAddEdit(Category $category, $template)
    {
        $form = $this->createForm('anh_content_form_type_category', $category);
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $this->getCategoryManager()->save($category);

                return $this->redirect($this->generateUrl('anh_content_admin_category_list'));
            }
        }

        $options = $this->container->getParameter('anh_content.options');
        $sections = $this->container->getParameter('anh_content.sections');

        return $this->render($template, array(
            'sections' => $sections,
            'options' => $options,
            'form' => $form->createView()
        ));
    }

    public function categoryDeleteAction()
    {
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $list = $request->request->get('id');

            if (!empty($list) and is_array($list)) {
                $this->getCategoryManager()->deleteByIdList($list);
            }
        }

        return $this->redirect($this->generateUrl('anh_content_admin_category_list'));
    }

    private function getCategoryManager()
    {
        return $this->container->get('anh_content.manager.category');
    }

    private function getPaperManager()
    {
        return $this->container->get('anh_content.manager.paper');
    }
}
