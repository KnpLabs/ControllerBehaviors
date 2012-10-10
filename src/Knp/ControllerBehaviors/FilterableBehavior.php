<?php

namespace Knp\ControllerBehaviors;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;

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
    abstract protected function getObjectName();

    /**
     * Creates route for list CRUD action.
     *
     * @param  mixed $entity entity instance
     *
     * @return string
     */
    abstract protected function getListRoute();

    abstract protected function getListResponse();

    /**
     * Displays a form to edit an existing User entity.
     *
     */
    public function getFilterResponse(Request $request)
    {
        $form = $this->createFilterForm();
        $form->bindRequest($request);

        if ($form->isValid()) {
            $this->setFilters($form->getData());

            return $this->redirect($this->getListRoute());
        }

        return $this->getInvalidFilterResponse($form);
    }

    public function getInvalidFilterResponse(Form $form)
    {
        return $this->listAction();
    }

    public function getFilterResetResponse()
    {
        $this->setFilters(array());

        return $this->redirect($this->getListRoute());
    }

    private function setFilters(array $filters)
    {
        $this->get('session')->set(strtolower($this->getObjectName()).'-filters', $filters);
    }

    private function getFilters(array $defaults = array())
    {
        return $this->get('session')->get(strtolower($this->getObjectName()).'-filters', $defaults);
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
            $this->getObjectName()
        );

        return $this->createForm(new $formClass(), $this->getFilters());
    }
}
