<?php

namespace Knp\ControllerBehaviors\Crudable\Doctrine;
use Symfony\Component\HttpKernel\Kernel;

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
        if (Kernel::MINOR_VERSION < 2) {
            return $this->getDoctrine()->getEntityManager();
        } else {
            return $this->getDoctrine()->getManager();
        }
    }
}
