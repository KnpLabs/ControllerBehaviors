<?php

namespace Knp\Controller\Behavior;

use Symfony\Component\HttpFoundation\Request;

/**
 * Filtarable controller behavior.
 */
trait FilterableBehavior
{
    /**
     * Returns CRUD controller bundle namespace.
     *
     * @return string
     */
    abstract protected function getBundleNamespace();

    /**
     * Returns CRUD entity name.
     *
     * @return string
     */
    abstract protected function getEntityName();

    /**
     * Creates route for list CRUD action.
     *
     * @param  mixed $entity entity instance
     *
     * @return string
     */
    abstract protected function getListRoute();

    /**
     * Displays a form to edit an existing User entity.
     *
     */
    public function filterAction(Request $request)
    {
        $form = $this->createFilterForm();
        $form->bindRequest($request);

        if ($form->isValid()) {
            $this->setFilters($form->getData());

            return $this->redirect($this->getListRoute());
        }
    }

    public function filterResetAction()
    {
        $this->setFilters(array());

        return $this->redirect($this->getListRoute());
    }

    private function setFilters(array $filters)
    {
        $this->get('session')->set(strtolower($this->getEntityName()).'-filters', $filters);
    }

    private function getFilters(array $defaults = array())
    {
        return $this->get('session')->get(strtolower($this->getEntityName()).'-filters', $defaults);
    }

    /**
     * Created new filter form
     *
     * @return FormType
     */
    private function createFilterForm()
    {
        $formClass = sprintf('%s\\Form\\%sFilterType',
            $this->getBundleNamespace(),
            $this->getEntityName()
        );

        return $this->createForm(new $formClass(), $this->getFilters());
    }
}
