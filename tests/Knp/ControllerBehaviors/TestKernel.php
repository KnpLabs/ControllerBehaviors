<?php

namespace Test\Knp\ControllerBehaviors;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class TestKernel extends Kernel
{
    public function __construct()
    {
        parent::__construct('test', true);
    }

    public function registerBundles()
    {
        $bundles = array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
        );

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function($container) {
            $container->loadFromExtension('doctrine', [
                'dbal' => [
                    'driver' => 'pdo_sqlite',
                    'memory' => true,
                ],
                'orm' => [
                    'mappings' => [
                        'test' => [
                            'type'      => 'staticphp',
                            'dir'       => __DIR__.'/Entity',
                            'prefix'    => 'Tests\Knp\ControllerBehaviors',
                            'is_bundle' => false,
                        ],
                    ],
                ],
            ]);

            return $container;
        });
    }
}

