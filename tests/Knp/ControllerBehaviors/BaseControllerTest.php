<?php

namespace Tests\Knp\ControllerBehaviors;

use Test\Knp\ControllerBehaviors\TestKernel;
use Doctrine\ORM\Tools\SchemaTool;

require_once __DIR__.'/Entity/Test.php';

class BaseControllerTest extends \PHPUnit_Framework_TestCase
{
    protected function getContainer()
    {
        $kernel = new TestKernel;
        $kernel->boot();

        return $kernel->getContainer();
    }

    protected function resetDataBase()
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $schema = array_map(function($class) use ($em) {
            return $em->getClassMetadata($class);
        }, [
            'Tests\Knp\ControllerBehaviors\Entity\Test'
        ]);

        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema($schema);
        $schemaTool->createSchema($schema);
    }
}

