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

    public function paginateTags($page, $limit) // XXX WTF why this is here???
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
            ->findInSectionDQL($section)
        ;

        return $this->pager->paginate($query, $page, $limit);
    }

    public function findPublishedInSection($section, $modifiedSince = null)
    {
        return $this->repository
            ->findPublishedInSectionDQL($section, $modifiedSince)
            ->getResult()
        ;
    }

    public function paginatePublishedInSection($section, $page, $limit)
    {
        $query = $this->repository
            ->findPublishedInSectionDQL($section)
        ;

        return $this->pager->paginate($query, $page, $limit);
    }

    public function findPublishedInSectionAndCategory($section, Category $category, $modifiedSince = null)
    {
        return $this->repository
            ->findPublishedInSectionAndCategoryDQL($section, $category, $modifiedSince)
            ->getResult()
        ;
    }

    public function paginatePublishedInSectionAndCategory($section, $category, $page, $limit)
    {
        $query = $this->repository
            ->findPublishedInSectionAndCategoryDQL($section, $category)
        ;

        return $this->pager->paginate($query, $page, $limit);
    }

    public function findPublishedWithImageInSection($section)
    {
        return $this->repository
            ->findPublishedWithImageInSectionDQL($section)
            ->getResult()
        ;
    }

    public function paginatePublishedWithImageInSection($section, $page, $limit)
    {
        $query = $this->repository
            ->findPublishedWithImageInSectionDQL($section)
        ;

        return $this->pager->paginate($query, $page, $limit);
    }

    public function findMaxPublishedUpdatedAtInSection($section)
    {
        return $this->repository
            ->findMaxPublishedUpdatedAtInSectionDQL($section)
            ->getSingleScalarResult()
        ;
    }

    public function findMaxPublishedUpdatedAtInSectionAndCategory($section, Category $category)
    {
        return $this->repository
            ->findMaxPublishedUpdatedAtInSectionAndCategoryDQL($section, $category)
            ->getSingleScalarResult()
        ;
    }
}
