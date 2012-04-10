<?php

namespace Knp\ControllerBehaviors\Paginatable;

use Doctrine\ORM\QueryBuilder;

trait KnpPaginatorBehavior
{
    protected function paginateQueryBuilder(QueryBuilder $qb)
    {
        return $this->get('knp_paginator')->paginate(
            $qb,
            $this->get('request')->query->get('page', 1),
            $this->getNumberOfEntitiesPerPage()
        );
    }

    protected function getNumberOfEntitiesPerPage()
    {
        return 10;
    }
}
