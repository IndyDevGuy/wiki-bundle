<?php
namespace IndyDevGuy\Bundle\WikiBundle\Twig\Extension\MarkdownEngine;

use Aptoma\Twig\Extension\MarkdownEngineInterface;
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\Block\Element\IndentedCode;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use Spatie\CommonMarkHighlighter\FencedCodeRenderer;
use Spatie\CommonMarkHighlighter\IndentedCodeRenderer;

/**
 * PHPLeagueCommonMarkEngine.php
 *
 * Maps League\CommonMark\CommonMarkConverter to Aptoma\Twig Markdown Extension
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class PHPLeagueCommonMarkEngine implements MarkdownEngineInterface
{
    /**
     * @var CommonMarkConverter
     */
    private $converter;

    private $environment;

    /**
     * Constructor
     *
     * Accepts CommonMarkConverter or creates one automatically
     *
     * @param CommonMarkConverter $converter
     */
    public function __construct(CommonMarkConverter $converter = null)
    {
        $this->environment = Environment::createCommonMarkEnvironment();
        $this->environment->addExtension(new GithubFlavoredMarkdownExtension());
        $this->environment->addBlockRenderer(FencedCode::class, new FencedCodeRenderer());
        $this->environment->addBlockRenderer(IndentedCode::class, new IndentedCodeRenderer());
        $this->converter = $converter ?: new CommonMarkConverter([], $this->environment);
    }

    /**
     * {@inheritdoc}
     */
    public function transform($content)
    {
        return $this->converter->convertToHtml($content);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'League\CommonMark';
    }
}
