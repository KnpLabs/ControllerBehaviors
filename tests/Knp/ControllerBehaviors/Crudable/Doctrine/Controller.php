<?php

namespace Knp\ControllerBehaviors\Crudable\Doctrine;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerAware;

require_once __DIR__.'/../../Entity/Test.php';

class Controller extends ContainerAware
{
    use ORMBehavior;

    public function getDoctrine()
    {
        return $this->container->get('doctrine');
    }

    public function getRequest()
    {
        return new Request;
    }

    /**
     * Returns controller bundle shortname.
     *
     * @return string
     */
    protected function getBundleName()
    {
        return '';
    }

    protected function getBundleNamespace()
    {
        return '';
    }

    protected function getObjectNamespace()
    {
        return 'Tests\Knp\ControllerBehaviors\Entity';
    }

    protected function getObjectName()
    {
        return 'Test';
    }
}

