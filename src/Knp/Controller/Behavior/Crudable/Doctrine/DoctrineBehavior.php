<?php

namespace Knp\Controller\Behavior\Crudable\Doctrine;

use Knp\Controller\Behavior\Crudable\Crudable;

trait DoctrineBehavior
{
    use CrudableBehavior;

    /**
     * Shortcut to return the Doctrine Registry service.
     *
     * @return Registry
     *
     * @throws \LogicException If DoctrineBundle is not available
     */
    abstract public function getDoctrine();

    /**
     * Returns object manager instance.
     *
     * @return ObjectManager
     */
    abstract protected function getObjectManager();

    /**
     * Returns object repository.
     *
     * @param  string $alias object alias (optional - current CRUD entity by default)
     *
     * @return ObjectRepository
     */
    protected function getObjectRepository($alias = null)
    {
        return $this->getObjectManager()->getRepository($alias ?: $this->getObjectClass());
    }

    /**
     * Returns entities to list.
     *
     * @return Collection
     */
    protected function getObjectsToList()
    {
        return $this->getObjectRepository()->findAll();
    }

    /**
     * Returns object to show.
     *
     * @return Collection
     */
    protected function getObjectToShow($id)
    {
        return $this->getObjectRepository()->find($id);
    }

    /**
     * Persists object after successfull validation.
     *
     * @param mixed $object
     */
    protected function saveObject($object)
    {
        $this->getObjectManager()->persist($object);
        $this->getObjectManager()->flush();
    }

    /**
     * Removes object from database.
     *
     * @param mixed $object
     */
    protected function removeObject($object)
    {
        $this->getObjectManager()->remove($object);
        $this->getObjectManager()->flush();
    }
}
