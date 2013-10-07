<?php

namespace Anh\Bundle\ContentBundle;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

abstract class AbstractModelManager
{
    /**
     * Holds the Doctrine entity manager for db interaction
     * @var EntityManager
     */
    protected $em;

    /**
     * The fully-qualified class name for our entity
     * @var string
     */
    protected $class;

    /**
     * Entity-specific repo, useful for finding entities, for example
     * @var EntityRepository
     */
    protected $repository;

    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $this->class = $class;
    }

    /**
     * Returns the user's fully qualified class name.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Create entity
     *
     * @return Entity
     */
    public function create()
    {
        $class = $this->getClass();

        return new $class;
    }

    /**
     * Save entity
     * @return AbstractModelManager
     */
    public function save($entity, $flush = true)
    {
        $this->em->persist($entity);

        if ($flush) {
            $this->em->flush();
        }

        return $this;
    }

    /**
     * Delete entity
     * @return AbstractModelManager
     */
    public function delete($entity, $flush = true)
    {
        $this->em->remove($entity);

        if ($flush) {
            $this->em->flush();
        }

        return $this;
    }

    /**
     * Delete entities specified by array of id
     * Using em->getReference() in order to not fetch whole entity from db
     * see {@link http://stackoverflow.com/questions/11486662/doctrine-entity-remove-vs-delete-query-performance-comparison}
     *
     * @return AbstractModelManager
     */
    public function deleteByIdList(array $list)
    {
        foreach ($list as $id) {
            $entity = $this->em->getReference($this->class, $id);
            if (!empty($entity)) {
                $this->delete($entity, false); // do not flush each delete
            }
        }

        $this->em->flush();

        return $this;
    }

    /**
     * Fetch one entity
     */
    public function find($id)
    {
        return $this->repository->findOneBy(array('id' => $id));
    }

    /**
     * Fetch all entities
     */
    public function findAll()
    {
        return $this->repository->findAll();
    }
}
