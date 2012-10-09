<?php

namespace Knp\ControllerBehaviors\Crudable;

use Knp\ControllerBehaviors\Crudable\CrudableBehavior;

trait DefaultNamingBehavior
{
    public function listAction()
    {
        return $this->getListResponse();
    }

    public function showAction($id)
    {
        return $this->getShowResponse($id);
    }

    public function newAction()
    {
        return $this->getNewResponse();
    }

    public function createAction()
    {
        return $this->getCreateResponse();
    }

    public function editAction($id)
    {
        return $this->getEditResponse($id);
    }

    public function updateAction($id)
    {
        return $this->getUpdateResponse($id);
    }

    public function deleteAction($id)
    {
        return $this->getDeleteResponse($id);
    }
}

