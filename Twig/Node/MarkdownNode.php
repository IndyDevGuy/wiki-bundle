<?php

namespace IndyDevGuy\Bundle\WikiBundle\Twig\Node;

/**
 * Represents a markdown node.
 *
 * It parses content as Markdown.
 *
 * @author Gunnar Lium <gunnar@aptoma.com>
 * @author Joris Berthelot <joris@berthelot.tel>
 */
class MarkdownNode extends \Twig\Node\Node
{
    public function __construct(\Twig\Node\Node $body, $lineno, $tag = 'markdown')
    {
        parent::__construct(array('body' => $body), array(), $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param \Twig\Compiler A Twig\Compiler instance
     */
    public function compile(\Twig\Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('ob_start();' . PHP_EOL)
            ->subcompile($this->getNode('body'))
            ->write('$content = ob_get_clean();' . PHP_EOL)
            ->write('preg_match("/^\s*/", $content, $matches);' . PHP_EOL)
            ->write('$lines = explode("\n", $content);' . PHP_EOL)
            ->write('$content = preg_replace(\'/^\' . $matches[0]. \'/\', "", $lines);' . PHP_EOL)
            ->write('$content = join("\n", $content);' . PHP_EOL)
            ->write('echo $this->env->getExtension(\'Aptoma\Twig\Extension\MarkdownExtension\')->parseMarkdown($content);' . PHP_EOL);
    }
}
