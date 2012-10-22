<?php

namespace spec\example\Filterable;

use PHPSpec2\ObjectBehavior;

class Controller extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('example\Filterable\Controller');
    }
}
