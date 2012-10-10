<?php

namespace Knp\ControllerBehaviors\Crudable\Doctrine;

/**
 * ORMBehavior.
 */
trait ORMBehavior
{
    use DoctrineBehavior;
    /**
     * Returns namespace prefix for crudable objects.
     *
     * @return string
     */
    protected function getObjectNamespace()
    {
        return sprintf('%s\\Entity', $this->getBundleNamespace());
    }

    /**
     * Returns object manager instance.
     *
     * @return ObjectManager
     */
    protected function getObjectManager()
    {
        return $this->getDoctrine()->getEntityManager();
    }
}
