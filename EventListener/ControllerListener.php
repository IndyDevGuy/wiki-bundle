<?php
namespace IndyDevGuy\WikiBundle\EventListener;

use IndyDevGuy\WikiBundle\Controller\WikiBaseController;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class ControllerListener
{
    private $twig;
    private $container;
    public function __construct( Environment $twig, Container $container)
    {
        $this->twig = $twig;
        $this->container = $container;
    }
    /**
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();
        if ($controller instanceof WikiBaseController) {
            $this->twig->addGlobal('highlightJsTheme', $this->container->getParameter('wiki_bundle.highlight_js_theme'));
        }
    }
}