<?php

namespace Anh\Bundle\ContentBundle\Entity;

use Anh\Bundle\ContentBundle\AbstractModelManager;

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
