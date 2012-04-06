<?php

namespace Knp\Controller\Behavior;

use Symfony\Component\HttpFoundation\Request;

/**
 * Bulk action controller behavior.
 */
trait BulkBehavior
{
    public function bulkAction(Request $request)
    {
        $ids = $request->get('ids', array());
        $action = $request->get('batch_action');

        if (!$this->hasBulkAction($action)) {
            $this->get('session')->setFlash('error', $this->generateTranslationKey('Invalid bulk action'));

            return $this->redirect($this->getListRoute());
        }

        if (!$ids) {
            $this->get('session')->setFlash('error', $this->generateTranslationKey('Empty bulk ids'));

            return $this->redirect($this->getListRoute());
        }

        $bulkAction = $this->getBulkActions()[$action];

        return $this->$bulkAction($ids);
    }

    /**
     * @param array $ids
     */
    public function bulkDeleteAction($ids = array())
    {
        $qb = $this
            ->getEntityRepository()
            ->createQueryBuilder('e')
            ->delete()
        ;

        $numDeleted = $qb
            ->where($qb->expr()->in('e.id', $ids))
            ->getQuery()
            ->execute()
        ;

        if ($numDeleted) {
            $this->get('session')->setFlash('success', $this->generateTranslationKey('Successful bulk delete'));
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
}
