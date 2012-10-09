<?php

namespace Knp\ControllerBehaviors\Crudable;

use Knp\ControllerBehaviors\Crudable\CrudableBehavior;

trait FOSRestBehavior
{
    public function listAction()
    {
        return $this->getListResponse();
    }

    public function getAction($id)
    {
        return $this->getShowResponse($id);
    }

    public function newAction()
    {
        return $this->getNewResponse();
    }

    public function postAction($id)
    {
        return $this->getCreateResponse($id);
    }

    public function editAction($id)
    {
        return $this->getEditResponse($id);
    }

    public function putAction($id)
    {
        return $this->getUpdateResponse($id);
    }

    public function deleteAction($id)
    {
        return $this->getDeleteResponse($id);
    }
}

