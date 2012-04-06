<?php

namespace Knp\Controller\Behavior;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Util\Inflector;

/**
 * CRUD controller behavior.
 */
trait CrudableBehavior
{
    /**
     * Lists all entities.
     *
     * @param Request $request
     */
    public function listAction(Request $request)
    {
        $entities = $this->getEntitiesToList();

        return $this->render($this->getListViewPath(), $this->getListViewParameters([
            $this->getEntityPlural() => $entities,
        ]));
    }

    /**
     * Finds specific entity to display.
     */
    public function showAction($id)
    {
        $entity = $this->getEntityToShow($id);

        if (!$entity) {
            throw $this->createEntityNotFoundException($id);
        }

        return $this->showEntity($entity);
    }

    /**
     * Displays specific entity.
     *
     * @param  mixed $entity entity instance
     *
     * @return Response
     */
    protected function showEntity($entity)
    {
        $deleteForm = $this->createDeleteForm($entity);

        return $this->render($this->getShowViewPath(), [
            $this->getEntitySingular() => $entity,
            'delete_form'              => ($deleteForm) ? $deleteForm->createView() : null,
        ]);
    }

    /**
     * Displays a form to create a new entity.
     */
    public function newAction()
    {
        $entity = $this->createNewEntity();
        $form   = $this->createNewForm($entity);

        return $this->render($this->getNewViewPath(), [
            $this->getEntitySingular() => $entity,
            'form'                     => $form->createView(),
        ]);
    }

    /**
     * Creates a new entity.
     */
    public function createAction()
    {
        $request = $this->getRequest();
        $entity  = $this->createNewEntity();
        $form    = $this->createNewForm($entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            if ($newEntity = $this->saveEntity($entity)) {
                $entity = $newEntity;
            }
            $this->getEntityManager()->flush();
            $this->get('session')->setFlash('success', $this->getCreateFlashMessage());

            return $this->redirect($this->getShowRoute($entity));
        }

        return $this->render($this->getNewViewPath(), [
            $this->getEntitySingular() => $entity,
            'form'                     => $form->createView(),
        ]);
    }

    /**
     * Persists entity after successfull validation.
     *
     * @param mixed $entity
     */
    protected function saveEntity($entity)
    {
        return $this->getEntityManager()->persist($entity);
    }

    /**
     * Finds specific entity to edit.
     */
    public function editAction($id)
    {
        $entity = $this->getEntityToEdit($id);

        if (!$entity) {
            throw $this->createEntityNotFoundException($id);
        }

        return $this->editEntity($entity);
    }

    /**
     * Displays specific entity edition page.
     *
     * @param  mixed $entity entity instance
     *
     * @return Response
     */
    protected function editEntity($entity)
    {
        $editForm   = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($entity);

        return $this->render($this->getEditViewPath(), [
            $this->getEntitySingular() => $entity,
            'edit_form'                => $editForm->createView(),
            'delete_form'              => ($deleteForm) ? $deleteForm->createView() : null,
        ]);
    }

    /**
     * Finds specific entity to update.
     */
    public function updateAction($id)
    {
        $entity = $this->getEntityToEdit($id);

        if (!$entity) {
            throw $this->createEntityNotFoundException($id);
        }

        return $this->updateEntity($entity);
    }

    /**
     * Updates specific entity.
     *
     * @param  mixed $entity entity instance
     *
     * @return Response
     */
    protected function updateEntity($entity)
    {
        $editForm   = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($entity);
        $request    = $this->getRequest();

        $editForm->bindRequest($request);
        if ($editForm->isValid()) {
            if ($newEntity = $this->saveEntity($entity)) {
                $entity = $newEntity;
            }
            $this->getEntityManager()->flush();
            $this->get('session')->setFlash('success',
                $this->getUpdateFlashMessage($entity)
            );

            return $this->redirect($this->getEditRoute($entity));
        }

        return $this->render($this->getEditViewPath(), [
            $this->getEntitySingular() => $entity,
            'edit_form'                => $editForm->createView(),
            'delete_form'              => ($deleteForm) ? $deleteForm->createView() : null,
        ]);
    }

    /**
     * Finds specific entity to delete.
     */
    public function deleteAction($id)
    {
        $entity = $this->getEntityToDelete($id);

        if (!$entity) {
            throw $this->createEntityNotFoundException($id);
        }

        return $this->deleteEntity($entity);
    }

    /**
     * Deletes specific entity.
     *
     * @param  mixed $entity entity instance
     *
     * @return Response
     */
    protected function deleteEntity($entity)
    {
        $form    = $this->createDeleteForm($entity);
        $request = $this->getRequest();

        $form->bindRequest($request);
        if ($form->isValid()) {
            $this->removeEntity($entity);
            $this->getEntityManager()->flush();
            $this->get('session')->setFlash('success',
                $this->getDeleteFlashMessage($entity)
            );
        }

        return $this->redirect($this->getListRoute());
    }

    /**
     * Removes entity from database.
     *
     * @param mixed $entity
     */
    protected function removeEntity($entity)
    {
        $this->getEntityManager()->remove($entity);
    }

    /**
     * Returns controller bundle shortname.
     *
     * @return string
     */
    protected function getBundleName()
    {
        foreach ($this->get('kernel')->getBundles() as $bundle) {
            if (false !== strpos(get_class($this), $bundle->getNamespace())) {
                return $bundle->getName();
            }
        }
    }

    /**
     * Returns CRUD controller bundle namespace.
     *
     * @return string
     */
    protected function getBundleNamespace()
    {
        return $this->get('kernel')->getBundle($this->getBundleName())->getNamespace();
    }

    /**
     * Returns controller group (used for views generation).
     *
     * @return string
     */
    protected function getControllerGroup()
    {
        return str_replace(
            $this->getBundleNamespace().'\\Controller\\', '', substr(get_class($this), 0, -10)
        );
    }

    /**
     * Returns routes prefix, used for this CRUD controller.
     *
     * @return string
     */
    protected function getRoutesPrefix()
    {
        return strtolower($this->getEntityName()).'_';
    }

    /**
     * Creates route for list CRUD action.
     *
     * @param  mixed $entity entity instance
     *
     * @return string
     */
    protected function getListRoute()
    {
        return $this->generateUrl($this->getRoutesPrefix().'list');
    }

    /**
     * Creates route for show CRUD action.
     *
     * @param  mixed $entity entity instance
     *
     * @return string
     */
    protected function getShowRoute($entity)
    {
        return $this->generateUrl($this->getRoutesPrefix().'show', ['id' => $entity->getId()]);
    }

    /**
     * Creates route for edit CRUD action.
     *
     * @param  mixed $entity entity instance
     *
     * @return string
     */
    protected function getEditRoute($entity)
    {
        return $this->generateUrl($this->getRoutesPrefix().'edit', ['id' => $entity->getId()]);
    }

    /**
     * Returns CRUD entity name.
     *
     * @return string
     */
    protected function getEntityName()
    {
        return basename(str_replace('\\', '/', $this->getControllerGroup()));
    }

    /**
     * Returns public name (flash messages and webpages) of entity.
     *
     * @param mixed|null $entity
     *
     * @return string
     */
    protected function getEntityPublicName($entity = null)
    {
        if (null !== $entity && null !== $entity->getId()) {
            return $this->getEntityName().'#'.$entity->getId();
        }

        return $this->getEntityName();
    }

    /**
     * Returns singular form of entity, used to create view variables.
     *
     * @return string
     */
    protected function getEntitySingular()
    {
        return strtolower($this->getEntityName());
    }

    /**
     * Returns plural form of entity, used to create view variables.
     *
     * @return string
     */
    protected function getEntityPlural()
    {
        return $this->getEntitySingular().'s';
    }

    /**
     * Returns current entity alias (BundleName:EntityName).
     *
     * @return string
     */
    protected function getEntityAlias()
    {
        return $this->getBundleName().':'.$this->getEntityName();
    }

    /**
     * Returns entity manager instance.
     *
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getDoctrine()->getEntityManager();
    }

    /**
     * Returns entity repository.
     *
     * @param  string $alias entity alias (optional - current CRUD entity by default)
     *
     * @return EntityRepository
     */
    protected function getEntityRepository($alias = null)
    {
        return $this->getEntityManager()->getRepository($alias ?: $this->getEntityAlias());
    }

    /**
     * Returns entities to list.
     *
     * @return Collection
     */
    protected function getEntitiesToList()
    {
        $filter = is_callable($met=[$this,'getFilters']) ? $met() : [];

        return $this->getEntityRepository()->findBy($filter);
    }

    /**
     * Extension point to add list view parameters.
     *
     * @param array $parameters
     */
    protected function getListViewParameters(array $parameters)
    {
        return $parameters;
    }

    /**
     * Returns entity to show.
     *
     * @return Collection
     */
    protected function getEntityToShow($id)
    {
        return $this->getEntityRepository()->find($id);
    }

    /**
     * Returns entity to edit.
     *
     * @return Collection
     */
    protected function getEntityToEdit($id)
    {
        return $this->getEntityToShow($id);
    }

    /**
     * Returns entity to delete.
     *
     * @return Collection
     */
    protected function getEntityToDelete($id)
    {
        return $this->getEntityToShow($id);
    }

    /**
     * Creates entity 404 exception.
     *
     * @param  mixed $id
     *
     * @return Exception
     */
    protected function createEntityNotFoundException($id)
    {
        return $this->createNotFoundException(sprintf('Unable to find %s with %d id.',
            $this->getEntityName(), $id
        ));
    }

    /**
     * Returns prefix for the view paths (bundle name + controller group).
     *
     * @return string
     */
    protected function getViewsPathPrefix()
    {
        return $this->getBundleName().':'.$this->getControllerGroup();
    }

    /**
     * Returns full path to list view.
     *
     * @return stirng
     */
    protected function getListViewPath()
    {
        return $this->getViewsPathPrefix().':list.html.twig';
    }

    /**
     * Returns full path to show view.
     *
     * @return string
     */
    protected function getShowViewPath()
    {
        return $this->getViewsPathPrefix().':show.html.twig';
    }

    /**
     * Returns full path to new view.
     *
     * @return string
     */
    protected function getNewViewPath()
    {
        return $this->getViewsPathPrefix().':new.html.twig';
    }

    /**
     * Returns full path to edit view.
     *
     * @return string
     */
    protected function getEditViewPath()
    {
        return $this->getViewsPathPrefix().':edit.html.twig';
    }

    /**
     * Creates new entity instance.
     *
     * @return mixed
     */
    protected function createNewEntity()
    {
        $class = sprintf('%s\\Entity\\%s',
            $this->getBundleNamespace(),
            $this->getEntityName()
        );

        return new $class();
    }

    /**
     * Created new form type
     *
     * @param  mixed $entity
     *
     * @return FormType
     */
    protected function createNewForm($entity)
    {
        $formClass = sprintf('%s\\Form\\%sType',
            $this->getBundleNamespace(),
            $this->getEntityName()
        );

        return $this->createForm(new $formClass(), $entity);
    }

    /**
     * Creates edit form type.
     *
     * @param  mixed $entity
     *
     * @return FormType
     */
    protected function createEditForm($entity)
    {
        return $this->createNewForm($entity);
    }

    /**
     * Creates delete form type.
     *
     * @param  mixed $entity
     *
     * @return FormType
     */
    protected function createDeleteForm($entity)
    {
        return $this->createFormBuilder(['id' => $entity->getId()])
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * Returns entitiy creation flash message.
     *
     * @return string
     */
    protected function getCreateFlashMessage()
    {
        return sprintf('New %s successfully created',
            $this->getEntityPublicName()
        );
    }

    /**
     * Returns entitiy updation flash message.
     *
     * @param mixed $entity
     *
     * @return string
     */
    protected function getUpdateFlashMessage($entity)
    {
        return sprintf('%s successfully updated',
            $this->getEntityPublicName($entity)
        );
    }

    /**
     * Returns entitiy deletion flash message.
     *
     * @param mixed $entity
     *
     * @return string
     */
    protected function getDeleteFlashMessage($entity)
    {
        return sprintf('%s successfully deleted',
            $this->getEntityPublicName($entity)
        );
    }
}
