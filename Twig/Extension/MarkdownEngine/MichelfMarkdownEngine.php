<?php

namespace IndyDevGuy\Bundle\WikiBundle\Twig\Extension\MarkdownEngine;

use IndyDevGuy\Bundle\WikiBundle\Twig\Extension\MarkdownEngineInterface;
use Michelf\MarkdownExtra;

/**
 * MichelfMarkdownEngine.php
 *
 * Maps Michelf\MarkdownExtra to Aptoma\Twig Markdown Extension
 *
 * @author Joris Berthelot <joris@berthelot.tel>
 */
class MichelfMarkdownEngine implements MarkdownEngineInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($content)
    {
        return MarkdownExtra::defaultTransform($content);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Michelf\Markdown';
    }
}
