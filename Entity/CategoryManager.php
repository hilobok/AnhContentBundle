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
}
