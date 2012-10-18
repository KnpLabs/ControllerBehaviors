<?php

namespace Tests\Knp\ControllerBehaviors\Entity;

use Doctrine\ORM\Mapping\ClassMetadata;

class Test
{
    public static function loadMetadata(ClassMetadata $metadata)
    {
        $metadata->mapField(array(
           'id' => true,
           'fieldName' => 'id',
           'type' => 'integer'
        ));

        $metadata->mapField(array(
           'fieldName' => 'label',
           'type' => 'string'
        ));
    }

    public $id;

    public $label;
}

