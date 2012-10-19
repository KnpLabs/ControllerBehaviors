<?php

namespace spec\example\Crudable\Doctrine;

use PHPSpec2\ObjectBehavior;

class Controller extends ObjectBehavior
{
    /**
     * @param Symfony\Component\DependencyInjection\Container $container
     * @param Symfony\Bundle\FrameworkBundle\Templating\DelegatingEngine $templating
     * @param Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     * @param Doctrine\ORM\EntityRepository $repository
     * @param Doctrine\ORM\EntityManager $entityManager
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param Symfony\Component\HttpFoundation\Response $response
     * @param Symfony\Component\Form\FormFactory $formFactory
     * @param Symfony\Component\Form\FormBuilder $formBuilder
     * @param Symfony\Component\Form\Form $form
     * @param Symfony\Component\Form\FormView $formView
     * @param Symfony\Component\Routing\Router $router
     * @param stdClass $object
     **/
    function let($container, $templating, $doctrine, $repository, $entityManager, $request, $response, $formFactory, $formBuilder, $form, $formView, $router)
    {
        $container->get('templating')->willReturn($templating);
        $container->get('doctrine')->willReturn($doctrine);
        $container->get('form.factory')->willReturn($formFactory);
        $container->get('request')->willReturn($request);
        $container->get('router')->willReturn($router);

        $router->generate(ANY_ARGUMENTS)->willReturn('url');

        $formFactory->createBuilder(ANY_ARGUMENTS)->willReturn($formBuilder);
        $formBuilder->getForm(ANY_ARGUMENTS)->willReturn($form);

        $formFactory->create(ANY_ARGUMENTS)->willReturn($form);

        $form->createView()->willReturn($formView);

        $doctrine->getRepository(ANY_ARGUMENT)->willReturn($repository);
        $doctrine->getEntityManager()->willReturn($entityManager);

        $repository->findAll()->willReturn([]);

        $templating->renderResponse(':example\Crudable\Doctrine\:list.html.twig', ['tests' => []])->willReturn($response);

        $this->setContainer($container);
    }

    function its_getListResponse_should_return_a_Response_object($templating, $response)
    {
        $this->getListResponse()->shouldReturn($response);
    }

    function its_getListResponse_should_render_a_list_of_Objects($templating, $repository, $response, $object)
    {
        $repository->findAll[-1]->willReturn([$object]);
        $templating->renderResponse[-1](':example\Crudable\Doctrine\:list.html.twig', ['tests' => [$object]])->shouldBeCalled();

        $this->getListResponse()->shouldReturn($response);
    }

    function its_getShowResponse_should_render_an_Object($object, $templating, $repository, $response, $formView)
    {
        $object->getId()->willReturn(1);
        $repository->find(1)->willReturn($object);

        $templating->renderResponse[-1](':example\Crudable\Doctrine\:show.html.twig', [
            'test'        => $object->getWrappedSubject(),
            'delete_form' => $formView->getWrappedSubject(),
        ])->shouldBeCalled();

        $this->getShowResponse(1)->shouldReturn($response);
    }

    function its_getShowResponse_should_throw_Exception_when_no_resource_found($templating, $repository, $response, $formView)
    {
        $repository->find(2)->willReturn(null);
        $templating->renderResponse[-1]->shouldNotBeCalled();

        $this->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')->during('getShowResponse', [2]);
    }

    function its_getEditResponse_should_render_a_Form($object, $templating, $repository, $response, $formView)
    {
        $object->getId()->willReturn(1);
        $repository->find(1)->willReturn($object);

        $templating->renderResponse[-1](':example\Crudable\Doctrine\:edit.html.twig', [
            'test'        => $object->getWrappedSubject(),
            'edit_form'   => $formView->getWrappedSubject(),
            'delete_form' => $formView->getWrappedSubject(),
        ])->shouldBeCalled();

        $this->getEditResponse(1)->shouldReturn($response);
    }

    /**
     * @param stdClass $objectFactory
     **/
    function its_getCreateResponse_should_save_the_Object_when_form_is_valid($object, $templating, $repository, $form, $entityManager, $objectFactory)
    {
        $object->getId()->willReturn(1);
        $repository->find(1)->willReturn($object);
        $form->isValid()->willReturn(true);

        $objectFactory->willReturn($object);
        $this->setObjectFactory($objectFactory);

        $templating->renderResponse[-1]->shouldNotBeCalled();

        $entityManager->persist(ANY_ARGUMENT)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $response = $this->getCreateResponse(1);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse');
    }

    function its_getUpdateResponse_should_save_the_Object_when_form_is_valid($object, $templating, $repository, $form, $entityManager)
    {
        $object->getId()->willReturn(1);
        $repository->find(1)->willReturn($object);
        $form->isValid()->willReturn(true);

        $templating->renderResponse[-1]->shouldNotBeCalled();

        $entityManager->flush()->shouldBeCalled();

        $response = $this->getUpdateResponse(1);
        $response->shouldBeAnInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse');
    }

    function its_getUpdateResponse_should_render_edit_form_when_form_is_invalid($object, $templating, $repository, $response, $form, $formView)
    {
        $object->getId()->willReturn(1);
        $repository->find(1)->willReturn($object);
        $form->isValid()->willReturn(false);

        $templating->renderResponse[-1](':example\Crudable\Doctrine\:edit.html.twig', [
            'test'        => $object->getWrappedSubject(),
            'edit_form'   => $formView->getWrappedSubject(),
            'delete_form' => $formView->getWrappedSubject(),
        ])->shouldBeCalled();

        $this->getEditResponse(1)->shouldReturn($response);
    }
}
