<?php

namespace Anh\Bundle\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Anh\Bundle\ContentBundle\Entity\Category;
use Anh\Bundle\ContentBundle\Entity\Document;

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

    public function documentListAction($section, $page = 1)
    {
        $sections = $this->container->getParameter('anh_content.sections');

        if (!in_array($section, array_keys($sections))) {
            throw new \InvalidArgumentException("Section '{$section}' not configured.");
        }

        $options = $this->container->getParameter('anh_content.options');

        $pager = $this->getDocumentManager()->paginateInSection($section, $page, 10);

        $pagerUrl = str_replace('0', '{page}', $this->generateUrl('anh_content_admin_document_list', array(
            'section' => $section,
            'page' => 0
        )));

        return $this->render('AnhContentBundle:Admin:document/list.html.twig', array(
            'sections' => $sections,
            'options' => $options,
            'section' => $section,
            'pager' => $pager,
            'pagerUrl' => $pagerUrl
        ));
    }

    public function documentAddAction($section)
    {
        $sections = $this->container->getParameter('anh_content.sections');

        if (!in_array($section, array_keys($sections))) {
            throw new \InvalidArgumentException("Section '{$section}' not configured.");
        }

        $document = $this->getDocumentManager()->create();
        $document->setSection($section);

        return $this->documentAddEdit($document, 'AnhContentBundle:Admin:document/add.html.twig');
    }

    public function documentEditAction($section, $id)
    {
        $sections = $this->container->getParameter('anh_content.sections');

        if (!in_array($section, array_keys($sections))) {
            throw new \InvalidArgumentException("Section '{$section}' not configured.");
        }

        $document = $this->getDocumentManager()->find($id);

        if ($section !== $document->getSection()) {
            throw new \InvalidArgumentException("Document '{$id}' not in '{$section}'.");
        }

        return $this->documentAddEdit($document, 'AnhContentBundle:Admin:document/edit.html.twig');
    }

    private function documentAddEdit($document, $template)
    {
        $form = $this->createDocumentForm($document);

        $request = $this->getRequest();

        $section = $document->getSection();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $this->getDocumentManager()->save($document);

                // return $this->redirect($this->generateUrl('anh_content_admin_document_list', array('section' => $section)));
            }
        }

        $options = $this->container->getParameter('anh_content.options');
        $sections = $this->container->getParameter('anh_content.sections');

        $assetManager = $this->container->get('anh_content.asset_manager');

        $path = array(
            'uploads' => $assetManager->getPath('uploads'),
            'thumbs' => $assetManager->getPath('thumbs')
        );

        // getting all available tags from parser
        $parser = $this->container->get('anh_markup.parser');
        $decoda = $parser->create('bbcode', '', array());
        $tags = array();
        foreach ($decoda->getFilters() as $filter) {
            $tags = array_merge($tags, array_keys($filter->getTags()));
        }

        // remove not valid tags
        $tags = array_filter($tags, function($value) { return preg_match('/^[_a-z0-9]+$/', $value); });

        return $this->render($template, array(
            'tags' => $tags,
            'sections' => $sections,
            'options' => $options,
            'section' => $section,
            'form' => $form->createView(),
            'path' => $path
        ));
    }

    public function documentDeleteAction($section)
    {
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $list = $request->request->get('id');

            if (!empty($list) and is_array($list)) {
                $this->getDocumentManager()->deleteByIdList($list);
            }
        }

        return $this->redirect($this->generateUrl('anh_content_admin_document_list', array('section' => $section)));
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

    public function categoryEditAction($id)
    {
        $category = $this->getCategoryManager()->find($id);

        if (empty($category)) {
            throw new \InvalidArgumentException('Category not found.');
        }

        return $this->categoryAddEdit($category, 'AnhContentBundle:Admin:category/edit.html.twig');
    }

    private function categoryAddEdit($category, $template)
    {
        $form = $this->createCategoryForm($category);

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

    private function createCategoryForm(Category $category)
    {
        return $this->createForm('anh_content_form_type_category', $category);
    }

    private function createDocumentForm(Document $document)
    {
        return $this->createForm('anh_content_form_type_document', $document);
    }

    private function getCategoryManager()
    {
        return $this->container->get('anh_content.manager.category');
    }

    private function getDocumentManager()
    {
        return $this->container->get('anh_content.manager.document');
    }
}
