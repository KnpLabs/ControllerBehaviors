<?php

namespace Knp\ControllerBehaviors\Paginatable;

use Doctrine\ORM\QueryBuilder;

trait KnpPaginatorBehavior
{
    protected function paginateArray(array $data)
    {
        return $this->getKnpPaginator()->paginate(
            $data,
            $this->getRequest()->query->get('page', 1),
            $this->getNumberOfEntitiesPerPage()
        );
    }

    protected function paginateQueryBuilder(QueryBuilder $qb)
    {
        return $this->getKnpPaginator()->paginate(
            $qb,
            $this->getRequest()->query->get('page', 1),
            $this->getNumberOfEntitiesPerPage()
        );
    }

    abstract protected function getRequest();

    public function getKnpPaginator()
    {
        return $this->get('knp_paginator');
    }

    protected function getNumberOfEntitiesPerPage()
    {
        return 10;
    }
}
