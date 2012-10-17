# Symfony2 controller Traits

[![Build Status](https://secure.travis-ci.org/KnpLabs/ControllerBehaviors.png)](http://travis-ci.org/KnpLabs/ControllerBehaviors)


This php 5.4+ library is a collection of traits 
that adds behaviors to Symfony2 controllers.

It currently handles:

 * [crudable](#crudable) (Doctrine2 ORM and ODM)
 * [filterable](#filterable)
 * [paginable](#paginable)


## Usage

All you have to do is to define a Controller and use some traits.

<a name="crudable" id="crudable"></a>
### crudable:

Crudable trait is an abstract trait used internally by ORMBehavior and ODMBehavior.  
To use ORM persistence in your CRUD, just use ORMBehavior like below.


``` php

<?php

namespace Acme\DemoBundle\Controller;

use Knp\ControllerBehaviors\Crudable\Doctrine\ORMBehavior;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MemberController extends Controller
{
    // make aliases of actions to make them FOSRestBundle compliant

    use ORMBehavior {
        ORMBehavior::getListResponse   as getMembersAction;
        ORMBehavior::getShowResponse   as getMemberAction;
        ORMBehavior::getNewResponse    as newMembersAction;
        ORMBehavior::getEditResponse   as editMemberAction;
        ORMBehavior::getCreateResponse as postMembersAction;
        ORMBehavior::getUpdateResponse as putMemberAction;
        ORMBehavior::getDeleteResponse as deleteMemberAction;
    }
}

```


#### Form

The create, edit and update actions of Crudable  
will search for a `<Object>Type` in the `Form` folder of the bundle.

In the examples below, it would be in `src/Acme/DemoBundle/Form/MemberType`.

To modify this behavior, just override
the [default implementation](https://github.com/KnpLabs/ControllerBehaviors/blob/master/src/Knp/ControllerBehaviors/Crudable/CrudableBehavior.php#L544) 
of the trait, like this:

``` php

<?php

    protected function createNewForm($object)
    {
        return $this->createForm('my_super_type_id', $object, ['some_option' => true]);
    }

```

#### Templates

Templates are also searched using conventions. By default it will search in the `Resources\views/<ControllerName>` folder of your bundle.

`<ControllerName>` here can be contain a subfolder (think of an `Admin` subfolder for example).

To modify this behavior, just override
the [default implementation](https://github.com/KnpLabs/ControllerBehaviors/blob/master/src/Knp/ControllerBehaviors/Crudable/CrudableBehavior.php#L470) 
of the trait, like this:


``` php

<?php

    protected function getViewsPathPrefix()
    {
        return '::';
    }

```

### Filterable

Filterable behavior is a simple trait that stores and retrieves some informations for a given controller, 
like filter form data.

Once you posted data to `postFilterMembersAction`, you can retrieve it later by using  the `getFilters` method.

It also provides a way to handle a filter form, whose type yould be defined in `src/Acme/DemoBundle/Form/MemberFilterType` in this example.

``` php

<?php

use Knp\ControllerBehaviors\FilterableBehavior;

class MemberController extends Controller
{
    // make aliases of actions to make them FOSRestBundle compliant

    use FilterableBehavior {
        FilterableBehavior::getFilterResponse as postFilterMembersAction;
    }


}

```

In order to make this filter form visible in the view, you can override [default view parameters handling](https://github.com/KnpLabs/ControllerBehaviors/blob/master/src/Knp/ControllerBehaviors/Crudable/CrudableBehavior.php#L470):

``` php

<?php

    protected function getListViewParameters(array $parameters)
    {
        return array_merge([
            'filterForm'  => $this->createFilterForm()->createView(),
        ], $parameters);
    }

```


### Paginable

Paginable behavior is a simple trait that uses [Knp paginator](https://github.com/KnpLabs/KnpPaginatorBundle) to paginate a resultset.


``` php

<?php

use Knp\ControllerBehaviors\Paginatable\KnpPaginatorBehavior;

class MemberController extends Controller
{
    use KnpPaginatorBehavior;

    public function getObjectsToList()
    {
        return $this->paginateQueryBuilder(
            $this->getObjectRepository()->getJoinAllFilteredQB($this->getFilters()) // returns an ORM Query Builder
        );
    }
}

```
