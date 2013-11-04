<?php

namespace Anh\ContentBundle\Entity;

use Anh\ContentBundle\AbstractModelManager;
use Doctrine\ORM\EntityManager;
use Anh\Taggable\TaggableManager;

class PaperManager extends AbstractModelManager
{
    protected $pager;

    protected $taggableManager;

    public function __construct(EntityManager $em, $class, $pager, TaggableManager $taggableManager)
    {
        parent::__construct($em, $class);
        $this->pager = $pager;
        $this->taggableManager = $taggableManager;
    }

    /**
     * Returns taggable manager
     *
     * @return TaggableManager
     */
    public function getTaggableManager()
    {
        return $this->taggableManager;
    }

    /**
     * {@inhertidoc}
     */
    public function create()
    {
        $entity = parent::create();
        $entity->setTaggableManager($this->taggableManager);

        return $entity;
    }

    public function paginateTags($page, $limit)
    {
        $query = $this->taggableManager->getTagRepository()->findAllQB();

        return $this->pager->paginate($query, $page, $limit);
    }

    public function findInSectionBySlug($section, $slug)
    {
        return $this->repository
            ->findInSectionBySlugDQL($section, $slug)
            ->getSingleResult()
        ;
    }

    public function findInSection($section)
    {
        return $this->repository
            ->findInSectionDQL($section)
            ->getResult()
        ;
    }

    public function paginateInSection($section, $page, $limit)
    {
        $query = $this->repository
            ->findInSectionDQL($section);

        return $this->pager->paginate($query, $page, $limit);
    }

    public function findPublishedInSection($section)
    {
        return $this->repository
            ->findPublishedInSectionDQL($section)
            ->getResult()
        ;
    }

    public function paginatePublishedInSection($section, $page, $limit)
    {
        $query = $this->repository
            ->findPublishedInSectionDQL($section);

        return $this->pager->paginate($query, $page, $limit);
    }

    public function findPublishedInSectionAndCategory($section, $category)
    {
        return $this->repository
            ->findPublishedInSectionAndCategoryDQL($section, $category)
            ->getResult()
        ;
    }
}
