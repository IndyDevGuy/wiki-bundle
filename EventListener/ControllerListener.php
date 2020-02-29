<?php
namespace IndyDevGuy\WikiBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use IndyDevGuy\WikiBundle\Controller\WikiBaseController;
use IndyDevGuy\WikiBundle\Entity\WikiPage;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class ControllerListener
{
    private $twig;
    private $container;
    private $em;
    public function __construct( Environment $twig, EntityManagerInterface $em, Container $container)
    {
        $this->twig = $twig;
        $this->em = $em;
        $this->container = $container;
    }
    /**
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();
        if(is_array($controller)) {
            if ($controller[0] instanceof WikiBaseController) {
                $request = $event->getRequest();
                $parameters = explode('/', $request->getPathInfo());
                if(isset($parameters[2]) && $parameters[2] != null) {
                    //we need to try and get a wiki page with this parameter
                    $wikiPageName = $parameters[3];
                    $wikiPageName = str_replace('-',' ',$wikiPageName);
                    $wikiPage = $this->em->getRepository(WikiPage::class)->findOneBy(['name' => $wikiPageName]);
                    if (isset($wikiPage) && $wikiPage != null && $wikiPage instanceof WikiPage) {
                        $this->twig->addGlobal('highlightJsTheme', $wikiPage->getHighlighttheme());
                        return;
                    }
                }
            }
        }
        $this->setDefaultHighlightJsTheme();
    }
    private function setDefaultHighlightJsTheme()
    {
        $this->twig->addGlobal('highlightJsTheme', $this->container->getParameter('wiki.highlight_js_theme'));
    }
}