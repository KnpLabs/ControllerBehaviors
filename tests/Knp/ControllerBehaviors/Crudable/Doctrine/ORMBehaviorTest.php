<?php

namespace Knp\ControllerBehaviors\Crudable\Doctrine;

use Tests\Knp\ControllerBehaviors\BaseControllerTest;

require_once __DIR__.'/../../BaseControllerTest.php';
require_once __DIR__.'/Controller.php';

class ORMBehaviorTest extends BaseControllerTest
{
    /**
     * @test
     **/
    public function should_get_list_of_objects_response()
    {
        $controller = $this->getController();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $controller->getListResponse());
    }

    private function getController()
    {
        $controller = new Controller;
        $controller->setContainer($this->getContainer());

        return $controller;
    }

    protected function setUp()
    {
        $this->resetDataBase();
    }
}

