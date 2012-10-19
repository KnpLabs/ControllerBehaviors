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
     * @param Symfony\Component\HttpFoundation\Response $response
     * @param Symfony\Component\Form\FormFactory $formFactory
     * @param Symfony\Component\Form\FormBuilder $formBuilder
     * @param Symfony\Component\Form\Form $form
     * @param Symfony\Component\Form\FormView $formView
     * @param stdClass $object
     **/
    function let($container, $templating, $doctrine, $repository, $entityManager, $response, $formFactory, $formBuilder, $form, $formView)
    {
        $container->get('templating')->willReturn($templating);
        $container->get('doctrine')->willReturn($doctrine);
        $container->get('form.factory')->willReturn($formFactory);

        $formFactory->createBuilder(ANY_ARGUMENTS)->willReturn($formBuilder);
        $formBuilder->getForm(ANY_ARGUMENTS)->willReturn($form);

        $form->createView()->willReturn($formView);

        $doctrine->getRepository('Test\Test')->willReturn($repository);
        $doctrine->getManager()->willReturn($entityManager);

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
            'test' => $object->getWrappedSubject(),
            'delete_form' => $formView->getWrappedSubject(),
        ])->shouldBeCalled();

        $this->getShowResponse(1)->shouldReturn($response);
    }

    function its_getShowResponse_should_throw_Exception_when_no_resource_found($object, $templating, $repository, $response, $formView)
    {
        $repository->find(2)->willReturn(null);
        $templating->renderResponse[-1]->shouldNotBeCalled();

        $this->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')->during('getShowResponse', [2]);
    }
}
