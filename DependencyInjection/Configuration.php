<?php
namespace IndyDevGuy\WikiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('wiki');
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('highlight_js_theme')
                    ->defaultValue('github')
                ->end()
                ->scalarNode('php_parser')
                    ->defaultValue('IndyDevGuy\Bundle\WikiBundle\Twig\Extension\MarkdownEngine\PHPLeagueCommonMarkEngine')
                ->end()
            ->end();

        return $treeBuilder;
    }
}