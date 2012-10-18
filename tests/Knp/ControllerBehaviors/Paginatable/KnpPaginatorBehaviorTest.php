<?php

namespace Test\Knp\ControllerBehaviors\Paginatable;

require_once __DIR__.'/Controller.php';

class KnpPaginatorBehaviorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     **/
    public function should_return_paginate_object_from_an_orm_query_builder()
    {
        $controller = new Controller;

        $this->assertInstanceOf('Knp\Component\Pager\Pagination\SlidingPagination', $controller->paginate());
    }
}

