<?php

namespace Anh\ContentBundle\Entity;

use Anh\ContentBundle\AbstractModelManager;

class CategoryManager extends AbstractModelManager
{
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
}
