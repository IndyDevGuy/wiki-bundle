<?php

namespace IndyDevGuy\WikiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class WikiExtension extends Extension implements PrependExtensionInterface
{
    private $themeLocation;
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('wiki.php_parser',$config['php_parser']);
        $this->themeLocation = $config['highlight_js_theme'];
        $container->setParameter('wiki.highlight_js_theme',$this->themeLocation);
        $definition = $container->getDefinition('markdown.engine');
        $definition->setClass($config['php_parser']);

        $this->addAnnotatedClassesToCompile([
            'IndyDevGuy\\WikiBundle\\Services\\**',
            'IndyDevGuy\\WikiBundle\\Repository\\**',
            'IndyDevGuy\\WikiBundle\\Controller\\**',
        ]);
    }

    public function prepend(ContainerBuilder $container)
    {
        foreach (array_keys($container->getExtensions()) as $name) {
            switch ($name) {
                case 'twig':
                    $container->prependExtensionConfig(
                        $name,
                        array('form_themes' => array('@Wiki/form/form.fields.html.twig'))
                    );
            }
        }
        $container->loadFromExtension(
            'doctrine',
            [
                'orm' => [
                    'mappings' => [
                        'WikiBundle' => [
                            'type' => 'annotation',
                            'dir' => '%kernel.project_dir%/../vendor/indydevguy/wiki-bundle/Entity',
                            'prefix' => 'IndyDevGuy\\WikiBundle\\Entity',
                        ],
                    ],
                ],
            ]
        );
    }
    public function getAlias()
    {
        return 'wiki';
    }
}
