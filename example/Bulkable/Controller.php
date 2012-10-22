<?php

namespace example\Bulkable;

use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\ControllerBehaviors\Crudable\Doctrine\ORMBehavior;
use Knp\ControllerBehaviors\BulkableBehavior;

class Controller extends ContainerAware
{
    use ORMBehavior;
    use BulkableBehavior {
        BulkableBehavior::getSession insteadof ORMBehavior;
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

