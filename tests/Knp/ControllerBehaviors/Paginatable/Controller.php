<?php

namespace Test\Knp\ControllerBehaviors\Paginatable;

use Knp\ControllerBehaviors\Paginatable\KnpPaginatorBehavior;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\Paginator;

class Controller
{
    use KnpPaginatorBehavior;

    public function paginate()
    {
        return $this->paginateArray(['test', 'test2']);
    }

    public function getKnpPaginator()
    {
        return new Paginator;
    }

    public function getRequest()
    {
        return new Request;
    }
}

