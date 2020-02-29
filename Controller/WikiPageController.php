<?php

namespace IndyDevGuy\WikiBundle\Controller;

use IndyDevGuy\WikiBundle\Entity\Wiki;
use IndyDevGuy\WikiBundle\Entity\WikiPage;
use IndyDevGuy\WikiBundle\Form\WikiPageType;
use IndyDevGuy\WikiBundle\Services\WikiEventService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Security("has_role('ROLE_ADMIN')") *
 * @Route("/wiki/{wikiName}")
 * @ParamConverter("wikiName", class="WikiBundle:Wiki", converter="wiki_converter")
 */
class WikiPageController extends WikiBaseController
{
    /**
     * @Route("/pages", name="wiki_page_index", methods="GET")
     * @Security("has_role('ROLE_SUPERUSER') || has_role('ROLE_ADMIN') || has_role('ROLE_WIKI') ")
     */
    public function index(Wiki $wiki): Response
    {
        if (!$wikiRoles = $this->getWikiPermission($wiki)) {
            throw new AccessDeniedException('Access denied!');
        }
        $this->pageTitle = $wiki->getName() . ' Page List';
        $wikiPageRepository = $this->get('IndyDevGuy\WikiBundle\Repository\WikiPageRepository');

        $data = $wikiRoles;
        $data['wikiPages'] = $wikiPageRepository->findByWikiId($wiki->getId());
        $data['wiki'] = $wiki;
        $data['pageTitle'] = $this->pageTitle;
        $this->get('twig')->addGlobal('pageTitle', $this->pageTitle);
        return $this->render('@Wiki/wiki_page/index.html.twig', $data);
    }

    /**
     * @Route("/pages/add", name="wiki_page_add", methods="GET|POST")
     * @Security("has_role('ROLE_SUPERUSER') || has_role('ROLE_ADMIN') || has_role('ROLE_WIKI') ")
     */
    public function addAction(Request $request, Wiki $wiki): Response
    {
        $wikiPage = new WikiPage();
        $wikiPage->setWiki($wiki);
        $this->pageTitle = 'Add Wiki Page to ' . $wiki->getName();
        $this->get('twig')->addGlobal('pageTitle', $this->pageTitle);
        return $this->getEditForm($request, $wikiPage, $this->get('IndyDevGuy\WikiBundle\Services\WikiEventService'));
    }

    /**
     * @Route("/{wikiPage}", name="wiki_page_view", methods="GET")
     * @ParamConverter("wikiPage", class="WikiBundle:WikiPage", converter="wiki_page_converter")
     * @Security("has_role('ROLE_SUPERUSER') || has_role('ROLE_ADMIN') || has_role('ROLE_WIKI') ")
     */
    public function viewAction(Wiki $wiki, WikiPage $wikiPage): Response
    {
        if (!$wikiRoles = $this->getWikiPermission($wiki)) {
            throw new AccessDeniedException('Access denied!');
        }
        if (!$wikiRoles['readRole']) {
            throw new AccessDeniedException('Access denied!');
        }


        $this->pageTitle = $wikiPage->getName();

        $data = $wikiRoles;
        $data['wikiPage'] = $wikiPage;
        $data['wiki'] = $wiki;
        $data['pageTitle'] = $this->pageTitle;
        $this->get('twig')->addGlobal('pageTitle', $this->pageTitle);
        return $this->render('@Wiki/wiki_page/view.html.twig', $data);
    }

    /**
     * @Route("/pages/{id}/edit", name="wiki_page_edit", methods="GET|POST")
     * @Security("has_role('ROLE_SUPERUSER') || has_role('ROLE_ADMIN') || has_role('ROLE_WIKI') ")
     */
    public function editAction(Request $request, Wiki $wiki, WikiPage $wikiPage): Response
    {
        $this->pageTitle = 'Edit Page ' . $wikiPage->getName();
        return $this->getEditForm($request, $wikiPage, $this->get('IndyDevGuy\WikiBundle\Services\WikiEventService'));
    }

    /**
     * @Route("/pages/{id}/delete", name="wiki_page_delete", methods="GET")
     * @Security("has_role('ROLE_SUPERUSER') || has_role('ROLE_ADMIN') || has_role('ROLE_WIKI') ")
     */
    public function deleteAction(Request $request, Wiki $wiki, WikiPage $wikiPage): Response
    {
        if (!$wikiRoles = $this->getWikiPermission($wiki)) {
            throw new AccessDeniedException('Access denied!');
        }
        if (!$wikiRoles['writeRole']) {
            throw new AccessDeniedException('Access denied!');
        }

        $this->get('IndyDevGuy\WikiBundle\Services\WikiEventService')->createEvent(
            'page.deleted',
            $wikiPage->getWiki()->getId(),
            json_encode([
                'deletedAt' => time(),
                'deletedBy' => $this->getUser()->getUsername(),
                'name' => $wikiPage->getName(),
            ]),
            $wikiPage->getId()
        );

        $em = $this->getDoctrine()->getManager();
        $em->remove($wikiPage);
        $em->flush();

        return $this->redirectToRoute('wiki_page_index', [
            'wikiName' => $wiki->getName(),
        ]);
    }

    protected function getEditForm($request, $wikiPage, WikiEventService $wikiEventService)
    {
        if (!$wikiRoles = $this->getWikiPermission($wikiPage->getWiki())) {
            throw new AccessDeniedException('Access denied!');
        }
        if (!$wikiRoles['writeRole']) {
            throw new AccessDeniedException('Access denied!');
        }

        $form = $this->createForm(WikiPageType::class, $wikiPage);
        $form->handleRequest($request);

        $add = !$wikiPage->getId();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($wikiPage);
            $em->flush();

            if ($add) {
                $wikiEventService->createEvent(
                    'page.created',
                    $wikiPage->getWiki()->getId(),
                    json_encode([
                        'createdAt' => time(),
                        'createdBy' => $this->getUser()->getUsername(),
                        'name' => $wikiPage->getName(),
                    ]),
                    $wikiPage->getId()
                );
            } else {
                $wikiEventService->createEvent(
                    'page.updated',
                    $wikiPage->getWiki()->getId(),
                    json_encode([
                        'updatedAt' => time(),
                        'updatedBy' => $this->getUser()->getUsername(),
                        'name' => $wikiPage->getName(),
                    ]),
                    $wikiPage->getId()
                );
            }

            return $this->redirectToRoute('wiki_page_index', [
                'wikiName' => $wikiPage->getWiki()->getName(),
            ]);
        }
        $this->get('twig')->addGlobal('pageTitle', $this->pageTitle);
        return $this->render('@Wiki/wiki_page/edit.html.twig', [
            'wikiPage' => $wikiPage,
            'form' => $form->createView(),
            'pageTitle' => $this->pageTitle
        ]);
    }
}
