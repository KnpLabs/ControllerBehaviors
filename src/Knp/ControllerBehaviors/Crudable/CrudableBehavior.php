<?php

namespace Knp\ControllerBehaviors\Crudable;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Util\Inflector;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * CRUD controller behavior.
 */
trait CrudableBehavior
{
    protected $objectFactory;
    /**
     * Lists all entities.
     *
     * @param Request $request
     */
    public function getListResponse()
    {
        $entities = $this->getObjectsToList();

        return $this->getRenderedResponse($this->getListViewPath(), $this->getListViewParameters([
            $this->getObjectPlural() => $entities,
        ]));
    }

    /**
     * Finds specific object to display.
     */
    public function getShowResponse($id)
    {
        $object = $this->getObjectToShow($id);

        if (!$object) {
            throw $this->createObjectNotFoundException($id);
        }

        return $this->showObject($object);
    }

    /**
     * Displays specific object.
     *
     * @param  mixed $object object instance
     *
     * @return Response
     */
    protected function showObject($object)
    {
        $deleteForm = $this->createDeleteForm($object);

        return $this->getRenderedResponse($this->getShowViewPath(), $this->getShowViewParameters([
            $this->getObjectSingular() => $object,
            'delete_form'              => ($deleteForm) ? $deleteForm->createView() : null,
        ]));
    }

    /**
     * Displays a form to create a new object.
     */
    public function getNewResponse()
    {
        $object = $this->createNewObject();
        $form   = $this->createNewForm($object);

        return $this->getRenderedResponse($this->getNewViewPath(), $this->getNewViewParameters([
            $this->getObjectSingular() => $object,
            'form'                     => $form->createView(),
        ]));
    }

    /**
     * Creates a new object.
     */
    public function getCreateResponse()
    {
        $request = $this->getRequest();
        $object  = $this->createNewObject();
        $form    = $this->createNewForm($object);
        $form->bindRequest($request);

        if ($form->isValid()) {
            if ($newObject = $this->saveObject($object)) {
                $object = $newObject;
            }
            $this->getSession()->setFlash('success', $this->getCreateFlashMessage());

            return $this->redirect($this->getShowRoute($object));
        }

        return $this->getRenderedResponse($this->getNewViewPath(), $this->getNewViewParameters([
            $this->getObjectSingular() => $object,
            'form'                     => $form->createView(),
        ]));
    }

    /**
     * Finds specific object to edit.
     */
    public function getEditResponse($id)
    {
        $object = $this->getObjectToEdit($id);

        if (!$object) {
            throw $this->createObjectNotFoundException($id);
        }

        return $this->editObject($object);
    }

    /**
     * Displays specific object edition page.
     *
     * @param  mixed $object object instance
     *
     * @return Response
     */
    protected function editObject($object)
    {
        $editForm   = $this->createEditForm($object);
        $deleteForm = $this->createDeleteForm($object);

        return $this->getRenderedResponse($this->getEditViewPath(), $this->getEditViewParameters([
            $this->getObjectSingular() => $object,
            'edit_form'                => $editForm->createView(),
            'delete_form'              => ($deleteForm) ? $deleteForm->createView() : null,
        ]));
    }

    /**
     * Finds specific object to update.
     */
    public function getUpdateResponse($id)
    {
        $object = $this->getObjectToEdit($id);

        if (!$object) {
            throw $this->createObjectNotFoundException($id);
        }

        return $this->updateObject($object);
    }

    /**
     * Updates specific object.
     *
     * @param  mixed $object object instance
     *
     * @return Response
     */
    protected function updateObject($object)
    {
        $editForm   = $this->createEditForm($object);
        $deleteForm = $this->createDeleteForm($object);
        $request    = $this->getRequest();

        $editForm->bindRequest($request);
        if ($editForm->isValid()) {
            if ($newObject = $this->saveObject($object)) {
                $object = $newObject;
            }
            $this->getSession()->setFlash('success',
                $this->getUpdateFlashMessage($object)
            );

            return $this->redirect($this->getEditRoute($object));
        }

        return $this->getRenderedResponse($this->getEditViewPath(), $this->getEditViewParameters([
            $this->getObjectSingular() => $object,
            'edit_form'                => $editForm->createView(),
            'delete_form'              => ($deleteForm) ? $deleteForm->createView() : null,
        ]));
    }

    /**
     * Finds specific object to delete.
     */
    public function getDeleteResponse($id)
    {
        $object = $this->getObjectToDelete($id);

        if (!$object) {
            throw $this->createObjectNotFoundException($id);
        }

        return $this->deleteObject($object);
    }

    /**
     * Shortcut to return the session service.
     *
     * @return Session
     */
    public function getSession()
    {
        return $this->getRequest()->getSession();
    }

    /**
     * Shortcut to return the request service.
     *
     * @return Request
     */
    abstract protected function getRequest();

    /**
     * Returns namespace prefix for crudable objects.
     *
     * @return string
     */
    abstract protected function getObjectNamespace();

    /**
     * Persists object after successfull validation.
     *
     * @param mixed $object
     */
    abstract protected function saveObject($object);

    /**
     * Removes object from database.
     *
     * @param mixed $object
     */
    abstract protected function removeObject($object);

    /**
     * Returns entities to list.
     *
     * @return Collection
     */
    abstract protected function getObjectsToList();

    /**
     * Returns object to show.
     *
     * @return Collection
     */
    abstract protected function getObjectToShow($id);

    /**
     * Deletes specific object.
     *
     * @param  mixed $object object instance
     *
     * @return Response
     */
    protected function deleteObject($object)
    {
        $form    = $this->createDeleteForm($object);
        $request = $this->getRequest();

        $form->bindRequest($request);
        if ($form->isValid()) {
            $this->removeObject($object);
            $this->getSession()->setFlash('success',
                $this->getDeleteFlashMessage($object)
            );
        }

        return $this->redirect($this->getListRoute());
    }

    /**
     * Returns controller bundle shortname.
     *
     * @return string
     */
    protected function getBundleName()
    {
        if ($this instanceof ContainerAware) {
            foreach ($this->container->get('kernel')->getBundles() as $bundle) {
                if (false !== strpos(get_class($this), $bundle->getNamespace())) {
                    return $bundle->getName();
                }
            }
        }

        throw new \RuntimeException(
            'getBundleName() method should return proper bundle name. Please override it.'
        );
    }

    /**
     * Returns CRUD controller bundle namespace.
     *
     * @return string
     */
    protected function getBundleNamespace()
    {
        if ($this instanceof ContainerAware) {
            return $this->container->get('kernel')->getBundle($this->getBundleName())->getNamespace();
        }

        throw new \RuntimeException(
            'getBundleNamespace() method should return proper bundle namespace. Please override it.'
        );
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
        return strtolower($this->getObjectName()).'_';
    }

    /**
     * Creates route for list CRUD action.
     *
     * @param  mixed $object object instance
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
     * @param  mixed $object object instance
     *
     * @return string
     */
    protected function getShowRoute($object)
    {
        return $this->generateUrl($this->getRoutesPrefix().'show', ['id' => $object->getId()]);
    }

    /**
     * Creates route for edit CRUD action.
     *
     * @param  mixed $object object instance
     *
     * @return string
     */
    protected function getEditRoute($object)
    {
        return $this->generateUrl($this->getRoutesPrefix().'edit', ['id' => $object->getId()]);
    }

    /**
     * Returns CRUD object name.
     *
     * @return string
     */
    protected function getObjectName()
    {
        return basename(str_replace('\\', '/', $this->getControllerGroup()));
    }

    /**
     * Returns public name (flash messages and webpages) of object.
     *
     * @param mixed|null $object
     *
     * @return string
     */
    protected function getObjectPublicName($object = null)
    {
        if (null !== $object && null !== $object->getId()) {
            return $this->getObjectName().'#'.$object->getId();
        }

        return $this->getObjectName();
    }

    /**
     * Returns singular form of object, used to create view variables.
     *
     * @return string
     */
    protected function getObjectSingular()
    {
        return strtolower($this->getObjectName());
    }

    /**
     * Returns plural form of object, used to create view variables.
     *
     * @return string
     */
    protected function getObjectPlural()
    {
        return $this->getObjectSingular().'s';
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

    protected function getShowViewParameters(array $parameters)
    {
        return $parameters;
    }

    protected function getNewViewParameters(array $parameters)
    {
        return $parameters;
    }

    protected function getEditViewParameters(array $parameters)
    {
        return $this->getNewViewParameters($parameters);
    }

    /**
     * Returns object to edit.
     *
     * @return Collection
     */
    protected function getObjectToEdit($id)
    {
        return $this->getObjectToShow($id);
    }

    /**
     * Returns object to delete.
     *
     * @return Collection
     */
    protected function getObjectToDelete($id)
    {
        return $this->getObjectToShow($id);
    }

    /**
     * Creates object 404 exception.
     *
     * @param  mixed $id
     *
     * @return Exception
     */
    protected function createObjectNotFoundException($id)
    {
        throw new NotFoundHttpException(sprintf('Unable to find %s with %d id.',
            $this->getObjectName(), $id
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
     * Returns fully qualified class name of the crudable object.
     *
     * @return string
     */
    protected function getObjectClass()
    {
        return sprintf('%s\\%s', $this->getObjectNamespace(), $this->getObjectName());
    }

    /**
     * Creates new object instance.
     *
     * @return mixed
     */
    protected function createNewObject()
    {
        return $this->getObjectFactory()->create();
    }

    private function getObjectFactory()
    {
        return $this->objectFactory ?: $this->objectFactory = new ObjectFactory($this->getObjectClass());
    }

    public function setObjectFactory($objectFactory)
    {
        $this->objectFactory = $objectFactory;
    }

    /**
     * Created new form type
     *
     * @param  mixed $object
     *
     * @return FormType
     */
    protected function createNewForm($object, array $options = [])
    {
        if (!$this instanceof ContainerAware) {
            throw new \RuntimeException(
                'createNewForm() method should return a Form instance. Please override it.'
            );
        }

        $type = sprintf('%s\\Form\\%sType',
            $this->getBundleNamespace(),
            $this->getObjectName()
        );

        return $this->container->get('form.factory')->create(new $type, $object, $options);
    }

    /**
     * Creates edit form type.
     *
     * @param  mixed $object
     *
     * @return FormType
     */
    protected function createEditForm($object)
    {
        return $this->createNewForm($object);
    }

    /**
     * Creates delete form type.
     *
     * @param  mixed $object
     *
     * @return FormType
     */
    protected function createDeleteForm($object)
    {
        if (!$this instanceof ContainerAware) {
            throw new \RuntimeException(
                'createDeleteForm() method should return a Form instance. Please override it.'
            );
        }

        $builder = $this->container->get('form.factory')->createBuilder('form', ['id' => $object->getId()]);
        $builder->add('id', 'hidden');

        return $builder->getForm();
    }

    /**
     * Returns entitiy creation flash message.
     *
     * @return string
     */
    protected function getCreateFlashMessage()
    {
        return sprintf('New %s successfully created',
            $this->getObjectPublicName()
        );
    }

    /**
     * Returns entitiy updation flash message.
     *
     * @param mixed $object
     *
     * @return string
     */
    protected function getUpdateFlashMessage($object)
    {
        return sprintf('%s successfully updated',
            $this->getObjectPublicName($object)
        );
    }

    /**
     * Returns entitiy deletion flash message.
     *
     * @param mixed $object
     *
     * @return string
     */
    protected function getDeleteFlashMessage($object)
    {
        return sprintf('%s successfully deleted',
            $this->getObjectPublicName($object)
        );
    }

    private function getRenderedResponse($template, array $parameters)
    {
        if ($this instanceof ContainerAware) {
            return $this->container->get('templating')->renderResponse($template, $parameters);
        }
    }

    protected function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }

    public function generateUrl($route, $parameters = [], $absolute = false)
    {
        if (!$this instanceof ContainerAware) {
            throw new \RuntimeException(
                'generateUrl() method should return a url from a route name. Please override it.'
            );
        }

        return $this->container->get('router')->generate($route, $parameters, $absolute);
    }
}
