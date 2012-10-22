<?php

namespace example\Paginatable;

use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\ControllerBehaviors\Crudable\Doctrine\ORMBehavior;
use Knp\ControllerBehaviors\Paginatable\KnpPaginatorBehavior;

class Controller extends ContainerAware
{
    use ORMBehavior;
    use KnpPaginatorBehavior;

    public function getDoctrine()
    {
        return $this->container->get('doctrine');
    }

    public function getRequest()
    {
        return $this->container->get('request');
    }
}

