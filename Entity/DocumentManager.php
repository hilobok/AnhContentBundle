<?php

namespace Anh\Bundle\ContentBundle\Entity;

use Anh\Bundle\ContentBundle\AbstractModelManager;
use Doctrine\ORM\EntityManager;

class DocumentManager extends AbstractModelManager
{
    protected $pager;

    public function __construct(EntityManager $em, $class, $pager)
    {
        parent::__construct($em, $class);
        $this->pager = $pager;
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
