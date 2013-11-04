<?php

namespace Anh\ContentBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
    public function findInSectionBySlugDQL($section, $slug)
    {
        return $this->createQueryBuilder('c')
            ->where('c.section = :section')
            ->andWhere('c.slug = :slug')
            ->setParameters(array(
                'section' => $section,
                'slug' => $slug
            ))
            ->getQuery()
        ;
    }
}
