<?php

namespace Knp\ControllerBehaviors\Crudable\Propel;

use Knp\ControllerBehaviors\Crudable\CrudableBehavior;

trait PropelBehavior
{
    use CrudableBehavior;

    /**
     * Returns query object.
     *
     * @return ObjectRepository
     */
    protected function getQueryObject()
    {
        $class = $this->getObjectClass().'Query';

        return new $class;
    }

    /**
     * Returns objects to list.
     *
     * @return PropelObjectCollection
     */
    protected function getObjectsToList()
    {
        return $this->getQueryObject()->find();
    }

    /**
     * Returns object to show.
     *
     * @return Object
     */
    protected function getObjectToShow($id)
    {
        return $this->getQueryObject()->findPK($id);
    }

    /**
     * Persists object after successfull validation.
     *
     * @param mixed $object
     */
    protected function saveObject($object)
    {
        return $object->save();
    }

    /**
     * Removes object from database.
     *
     * @param mixed $object
     */
    protected function removeObject($object)
    {
        return $object->delete();
    }
}

