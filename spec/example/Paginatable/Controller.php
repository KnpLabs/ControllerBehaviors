<?php

namespace spec\example\Paginatable;

use PHPSpec2\ObjectBehavior;

class Controller extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('example\Paginatable\Controller');
    }
}
