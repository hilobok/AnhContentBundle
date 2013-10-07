<?php

namespace Anh\Bundle\ContentBundle\Entity;

use Doctrine\ORM\EntityRepository;

class DocumentRepository extends EntityRepository
{
    public function findInSectionBySlugDQL($section, $slug)
    {
        return $this->createQueryBuilder('d')
            ->where('d.section = :section')
            ->andWhere('d.slug = :slug')
            ->setParameters(array(
                'section' => $section,
                'slug' => $slug
            ))
            ->getQuery()
        ;
    }

    public function findInSectionDQL($section)
    {
        return $this->createQueryBuilder('d')
            ->where('d.section = :section')
            ->setParameter('section', $section)
            ->orderBy('d.publishedSince', 'DESC')
            ->getQuery()
        ;
    }

    public function findPublishedInSectionDQL($section)
    {
        return $this->createQueryBuilder('d')
            ->where('d.section = :section')
            ->andWhere('d.isDraft = :isDraft')
            ->andWhere('d.publishedSince <= current_timestamp()')
            ->setParameters(array(
                'section' => $section,
                'isDraft' => false
            ))
            ->orderBy('d.publishedSince', 'DESC')
            ->getQuery()
        ;
    }

    public function findPublishedInSectionAndCategoryDQL($section, $category)
    {
        return $this->createQueryBuilder('d')
            ->where('d.section = :section')
            ->andWhere('d.isDraft = :isDraft')
            ->andWhere('d.publishedSince <= current_timestamp()')
            ->andWhere('d.category = :category')
            ->setParameters(array(
                'section' => $section,
                'isDraft' => false,
                'category' => $category
            ))
            ->orderBy('d.publishedSince', 'DESC')
            ->getQuery()
        ;
    }
}
