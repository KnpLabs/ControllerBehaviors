<?php

namespace Knp\Controller\Behavior\Crudable\Doctrine;

/**
 * ODMBehavior.
 */
trait ODMBehavior
{
    use DoctrineBehavior;

    /**
     * Returns namespace prefix for crudable objects.
     *
     * @return string
     */
    protected function getObjectNamespace()
    {
        return sprintf('%s\\Document', $this->getBundleNamespace());
    }

    /**
     * Returns object manager instance.
     *
     * @return ObjectManager
     */
    protected function getObjectManager()
    {
        return $this->getDoctrine()->getDocumentManager();
    }
}
