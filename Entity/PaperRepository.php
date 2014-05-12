<?php

namespace Anh\ContentBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PaperRepository extends EntityRepository
{
    public function findInSectionBySlugDQL($section, $slug)
    {
        return $this->createQueryBuilder('paper')
            ->select('paper, category')
            ->leftJoin('paper.category', 'category')
            ->where('paper.section = :section')
            ->andWhere('paper.slug = :slug')
            ->setParameters(array(
                'section' => $section,
                'slug' => $slug
            ))
            ->getQuery()
        ;
    }

    public function findInSectionDQL($section)
    {
        return $this->createQueryBuilder('paper')
            ->select('paper, category')
            ->leftJoin('paper.category', 'category')
            ->where('paper.section = :section')
            ->setParameter('section', $section)
            ->orderBy('paper.publishedSince', 'DESC')
            ->getQuery()
        ;
    }

    public function findPublishedInSectionDQL($section, $modifiedSince = null)
    {
        $query = $this->createQueryBuilder('paper')
            ->where('paper.section = :section')
            ->andWhere('paper.isDraft = :isDraft')
            ->andWhere('paper.publishedSince <= current_timestamp()')
            ->setParameters(array(
                'section' => $section,
                'isDraft' => false
            ))
            ->orderBy('paper.publishedSince', 'DESC')
        ;

        if ($modifiedSince) {
            $query
                ->andWhere('paper.updatedAt > :modifiedSince')
                ->setParameter('modifiedSince', $modifiedSince)
            ;
        }

        return $query->getQuery();
    }

    public function findPublishedInSectionAndCategoryDQL($section, Category $category, $modifiedSince = null)
    {
        $query = $this->createQueryBuilder('paper')
            ->select('paper, category')
            ->leftJoin('paper.category', 'category')
            ->where('paper.section = :section')
            ->andWhere('paper.isDraft = :isDraft')
            ->andWhere('paper.publishedSince <= current_timestamp()')
            ->andWhere('paper.category = :category')
            ->setParameters(array(
                'section' => $section,
                'isDraft' => false,
                'category' => $category
            ))
            ->orderBy('paper.publishedSince', 'DESC')
        ;

        if ($modifiedSince) {
            $query
                ->andWhere('paper.updatedAt > :modifiedSince')
                ->setParameter('modifiedSince', $modifiedSince)
            ;
        }

        return $query->getQuery();
    }

    public function findPublishedWithImageInSectionDQL($section)
    {
        return $this->createQueryBuilder('paper')
            ->select('paper, category')
            ->leftJoin('paper.category', 'category')
            ->where('paper.section = :section')
            ->andWhere('paper.isDraft = :isDraft')
            ->andWhere('paper.publishedSince <= current_timestamp()')
            ->andWhere('paper.image > :image')
            ->setParameters(array(
                'section' => $section,
                'isDraft' => false,
                'image' => ''
            ))
            ->orderBy('paper.publishedSince', 'DESC')
            ->getQuery()
        ;
    }

    public function findMaxPublishedUpdatedAtInSectionDQL($section)
    {
        return $this->createQueryBuilder('paper')
            ->select('max(paper.updatedAt)')
            ->where('paper.section = :section')
            ->andWhere('paper.isDraft = :isDraft')
            ->andWhere('paper.publishedSince <= current_timestamp()')
            ->setParameters(array(
                'section' => $section,
                'isDraft' => false
            ))
            ->getQuery()
        ;
    }

    public function findMaxPublishedUpdatedAtInSectionAndCategoryDQL($section, Category $category)
    {
        return $this->createQueryBuilder('paper')
            ->select('max(paper.updatedAt)')
            ->where('paper.section = :section')
            ->andWhere('paper.isDraft = :isDraft')
            ->andWhere('paper.publishedSince <= current_timestamp()')
            ->andWhere('paper.category = :category')
            ->setParameters(array(
                'section' => $section,
                'isDraft' => false,
                'category' => $category
            ))
            ->getQuery()
        ;
    }
}
