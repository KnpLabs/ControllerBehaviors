<?php

namespace Knp\Controller\Behavior;

use Doctrine\ORM\QueryBuilder;

trait KnpPaginatableBehavior
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
