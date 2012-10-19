<?php

namespace example\Crudable\Doctrine;

use Tests\Knp\ControllerBehaviors\Entity\Test;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormBuilder;
use Knp\ControllerBehaviors\Crudable\Doctrine\ORMBehavior;

class Controller extends ContainerAware
{
    use ORMBehavior;

    public function getDoctrine()
    {
        return $this->container->get('doctrine');
    }

    public function getRequest()
    {
        return $this->container->get('request');
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
        return 'Test';
    }

    protected function getObjectName()
    {
        return 'Test';
    }

    public function createFormBuilder($data = null, array $options = array())
    {
        return $this->container->get('form.factory')->createBuilder('form', $data, $options);
    }
}

