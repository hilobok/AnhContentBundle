<?php

namespace Anh\ContentBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PaperRepository extends EntityRepository
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

    public function findPublishedInSectionDQL($section, $modifiedSince = null)
    {
        $query = $this->createQueryBuilder('d')
            ->where('d.section = :section')
            ->andWhere('d.isDraft = :isDraft')
            ->andWhere('d.publishedSince <= current_timestamp()')
            ->setParameters(array(
                'section' => $section,
                'isDraft' => false
            ))
            ->orderBy('d.publishedSince', 'DESC')
        ;

        if ($modifiedSince) {
            $query
                ->andWhere('d.updatedAt > :modifiedSince')
                ->setParameter('modifiedSince', $modifiedSince)
            ;
        }

        return $query->getQuery();
    }

    public function findPublishedInSectionAndCategoryDQL($section, Category $category, $modifiedSince = null)
    {
        $query = $this->createQueryBuilder('d')
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
        ;

        if ($modifiedSince) {
            $query
                ->andWhere('d.updatedAt > :modifiedSince')
                ->setParameter('modifiedSince', $modifiedSince)
            ;
        }

        return $query->getQuery();
    }

    public function findPublishedWithImageInSectionDQL($section)
    {
        return $this->createQueryBuilder('d')
            ->where('d.section = :section')
            ->andWhere('d.isDraft = :isDraft')
            ->andWhere('d.publishedSince <= current_timestamp()')
            ->andWhere('d.image > :image')
            ->setParameters(array(
                'section' => $section,
                'isDraft' => false,
                'image' => ''
            ))
            ->orderBy('d.publishedSince', 'DESC')
            ->getQuery()
        ;
    }

    public function findMaxPublishedUpdatedAtInSectionDQL($section)
    {
        return $this->createQueryBuilder('d')
            ->select('max(d.updatedAt)')
            ->where('d.section = :section')
            ->andWhere('d.isDraft = :isDraft')
            ->andWhere('d.publishedSince <= current_timestamp()')
            ->setParameters(array(
                'section' => $section,
                'isDraft' => false
            ))
            ->getQuery()
        ;
    }

    public function findMaxPublishedUpdatedAtInSectionAndCategoryDQL($section, Category $category)
    {
        return $this->createQueryBuilder('d')
            ->select('max(d.updatedAt)')
            ->where('d.section = :section')
            ->andWhere('d.isDraft = :isDraft')
            ->andWhere('d.publishedSince <= current_timestamp()')
            ->andWhere('d.category = :category')
            ->setParameters(array(
                'section' => $section,
                'isDraft' => false,
                'category' => $category
            ))
            ->getQuery()
        ;
    }
}
