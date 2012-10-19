<?php

namespace Knp\ControllerBehaviors\Crudable;

class ObjectFactory
{
    public function __construct($className)
    {
        $this->className = $className;
    }

    public function create()
    {
        return new $this->className;
    }
}

