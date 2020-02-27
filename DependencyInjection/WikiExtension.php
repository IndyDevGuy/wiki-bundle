<?php

namespace IndyDevGuy\Bundle\WikiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class WikiExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $this->addAnnotatedClassesToCompile([
            'IndyDevGuy\\Bundle\\WikiBundle\\Services\\**',
            'IndyDevGuy\\Bundle\\WikiBundle\\Repository\\**',
            'IndyDevGuy\\Bundle\\WikiBundle\\Controller\\**',
        ]);
    }

    public function prepend(ContainerBuilder $container)
    {
        foreach (array_keys($container->getExtensions()) as $name) {
            switch ($name) {
                case 'twig':
                    $container->prependExtensionConfig(
                        $name,
                        array('form_themes' => array('@Wiki/form/form.fields.twig'))
                    );
                    break;
            }
        }
        $container->loadFromExtension(
            'doctrine',
            [
                'orm' => [
                    'mappings' => [
                        'WikiBundle' => [
                            'type' => 'annotation',
                            'dir' => '%kernel.root_dir%/../vendor/indydevguy/wiki-bundle/Entity',
                            'prefix' => 'IndyDevGuy\\Bundle\\WikiBundle\\Entity',
                        ],
                    ],
                ],
            ]
        );
    }
}
