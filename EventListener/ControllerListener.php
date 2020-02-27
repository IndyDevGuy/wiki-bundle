<?php
namespace IndyDevGuy\Bundle\WikiBundle\EventListener;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
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
        $this->twig->addGlobal('highlightJsTheme',$this->container->getParameter('wiki.highlight_js_theme'));
    }
}