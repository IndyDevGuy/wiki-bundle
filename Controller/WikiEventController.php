<?php

namespace IndyDevGuy\Bundle\WikiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 * @Route("/wiki/{wikiName}/events")
 */
class WikiEventController extends WikiBaseController
{
    /**
     * @Route("/", name="wiki_event_index")
     * @Security("has_role('ROLE_SUPERUSER') || has_role('ROLE_ADMIN') || has_role('ROLE_WIKI') ")
     */
    public function indexAction($wikiName)
    {
        if (!$wiki = $this->get('IndyDevGuy\Bundle\WikiBundle\Repository\WikiRepository')->findOneByName($wikiName)) {
            return $this->redirectToRoute('wiki_index');
        }
        if (!$wikiRoles = $this->getWikiPermission($wiki)) {
            throw new AccessDeniedException('Access denied!');
        }
        if (!$wikiRoles['readRole']) {
            throw new AccessDeniedException('Access denied!');
        }

        $wikiEvents = $this->get('IndyDevGuy\Bundle\WikiBundle\Repository\WikiEventRepository')->findByWikiId($wiki->getId());

        return $this->render('@Wiki/wiki_event/index.html.twig', [
            'wikiEvents' => $wikiEvents,
            'wiki' => $wiki,
        ]);
    }

}
