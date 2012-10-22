<?php

namespace spec\example\Bulkable;

use PHPSpec2\ObjectBehavior;

class Controller extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('example\Bulkable\Controller');
    }
}
