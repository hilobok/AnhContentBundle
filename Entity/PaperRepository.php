<?php

namespace Anh\ContentBundle\Entity;

use Anh\DoctrineResource\ORM\ResourceRepository;

class PaperRepository extends ResourceRepository
{
    public function findLatestUpdateDate(array $criteria = null)
    {
        $queryBuilder = $this->prepareQueryBuilder($criteria, null);

        return $queryBuilder
            ->select('max(r.updatedAt)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
