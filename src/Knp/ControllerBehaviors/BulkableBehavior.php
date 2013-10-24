<?php

namespace Knp\ControllerBehaviors;

use Symfony\Component\HttpFoundation\Request;

/**
 * Bulk action controller behavior.
 */
trait BulkableBehavior
{
    public function bulkAction(Request $request)
    {
        $ids = $request->get('ids', array());
        $action = $request->get('batch_action');

        if (!$this->hasBulkAction($action)) {
            $this->getSession()->getFlashBag()->add('error', $this->getInvalidBulkActionFlashMessage());

            return $this->redirect($this->getListRoute());
        }

        if (!$ids) {
            $this->getSession()->getFlashBag()->add('error', $this->getEmptyBulkIdsFlashMessage());

            return $this->redirect($this->getListRoute());
        }

        $bulkAction = $this->getBulkActions()[$action];

        return $this->$bulkAction($ids);
    }

    abstract protected function getRequest();

    protected function getSession()
    {
        return $this->getRequest()->getSession();
    }

    /**
     * @param array $ids
     */
    public function bulkDeleteAction($ids = array())
    {
        $qb = $this
            ->getObjectRepository()
            ->createQueryBuilder('e')
            ->delete()
        ;

        $numDeleted = $qb
            ->where($qb->expr()->in('e.id', $ids))
            ->getQuery()
            ->execute()
        ;

        if ($numDeleted) {
            $this->getSession()->getFlashBag()->add('success', $this->getSuccessBulkDeleteFlashMessage());
        }

        return $this->redirect($this->getListRoute());
    }

    /**
     * @param string $action
     * @return string
     */
    protected function hasBulkAction($action)
    {
        return isset($this->getBulkActions()[$action]);
    }

    /**
     * @return array
     */
    protected function getBulkActions()
    {
        return ['delete' => 'bulkDeleteAction'];
    }

    protected function getSuccessBulkDeleteFlashMessage()
    {
        return 'Successful bulk delete';
    }

    protected function getInvalidBulkActionFlashMessage()
    {
        return 'Invalid bulk action';
    }

    protected function getEmptyBulkIdsFlashMessage()
    {
        return 'Empty bulk ids';
    }
}
