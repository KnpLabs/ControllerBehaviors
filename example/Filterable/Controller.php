<?php

namespace example\Filterable;

use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\ControllerBehaviors\FilterableBehavior;
use Knp\ControllerBehaviors\Crudable\Doctrine\ORMBehavior;

class Controller extends ContainerAware
{
    use ORMBehavior;
    use FilterableBehavior {
        FilterableBehavior::getSession insteadof ORMBehavior;
    }

    public function getDoctrine()
    {
        return $this->container->get('doctrine');
    }

    public function getRequest()
    {
        return $this->container->get('request');
    }
}

