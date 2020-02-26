<?php

namespace IndyDevGuy\Bundle\WikiBundle\Controller;

use IndyDevGuy\Bundle\WikiBundle\Entity\Wiki;
use IndyDevGuy\Bundle\WikiBundle\Form\WikiType;
use IndyDevGuy\Bundle\WikiBundle\Services\WikiEventService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 * @Route("/wiki")
 */
class WikiController extends AbstractController
{
    /**
     * @Route("/", name="wiki_index", methods="GET")
     * @Security("has_role('ROLE_SUPERUSER') || has_role('ROLE_WIKI') ")
     */
    public function indexAction(): Response
    {
        $wikis = $this->get('IndyDevGuy\Bundle\WikiBundle\Repository\WikiRepository')->findAll();

        $wikiArray = [];
        foreach ($wikis as $wiki) {
            if ($wikiRoles = $this->getWikiPermission($wiki)) {
                if ($wikiRoles['readRole']) {
                    $wikiArray[] = $wiki;
                }
            }
        }

        return $this->render(
            '@Wiki/wiki/index.html.twig',
            ['wikis' => $wikiArray]
        );
    }

    /**
     * @Security("has_role('ROLE_SUPERUSER')")
     * @Route("/add", name="wiki_add", methods="GET|POST")
     */
    public function AddAction(Request $request): Response
    {
        $wiki = new Wiki();

        return $this->getEditForm($request, $wiki, $this->get('IndyDevGuy\Bundle\WikiBundle\Services\WikiEventService'));
    }

    /**
     * @Security("has_role('ROLE_SUPERUSER')")
     * @Route("/{wikiName}/edit", name="wiki_edit", methods="GET|POST")
     * @ParamConverter("wiki", options={"mapping"={"wikiName"="name"}})
     */
    public function editAction(Request $request, Wiki $wiki): Response
    {
        return $this->getEditForm($request, $wiki, $this->get('IndyDevGuy\Bundle\WikiBundle\Services\WikiEventService'));
    }

    /**
     * @Security("has_role('ROLE_SUPERUSER')")
     * @Route("/{wikiName}/delete", name="wiki_delete", methods="GET")
     * @ParamConverter("wiki", options={"mapping"={"wikiName"="name"}})
     */
    public function deleteAction(Request $request, Wiki $wiki): Response
    {
        if (count($wiki->getWikiPages())) {
            $this->addFlash('error', 'The wiki cannot be deleted because of having a wiki-page.');
        } else {
            $this->get('IndyDevGuy\Bundle\WikiBundle\Services\WikiEventService')
                ->createEvent(
                    'wiki.deleted',
                    $wiki->getId(),
                    json_encode([
                        'deletedAt' => time(),
                        'deletedBy' => $this->getUser()->getUsername(),
                        'name' => $wiki->getName(),
                    ])
                );
            $em = $this->getDoctrine()->getManager();
            $em->remove($wiki);
            $em->flush();
        }

        return $this->redirectToRoute('wiki_index');
    }

    protected function getEditForm(Request $request, Wiki $wiki, WikiEventService $wikiEventService)
    {
        $form = $this->createForm(WikiType::class, $wiki);
        $form->handleRequest($request);

        $add = !$wiki->getid();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($wiki);
            $em->flush();

            if ($add) {
                $wikiEventService->createEvent(
                    'wiki.created',
                    $wiki->getId(),
                    json_encode([
                        'createdAt' => time(),
                        'createdBy' => $this->getUser()->getUsername(),
                        'name' => $wiki->getName(),
                        'description' => $wiki->getDescription(),
                    ])
                );
            } else {
                $wikiEventService->createEvent(
                    'wiki.updated',
                    $wiki->getId(),
                    json_encode([
                        'updatedAt' => time(),
                        'updatedBy' => $this->getUser()->getUsername(),
                        'name' => $wiki->getName(),
                        'description' => $wiki->getDescription(),
                    ])
                );
            }

            return $this->redirectToRoute('wiki_index');
        }

        return $this->render('@Wiki/wiki/edit.html.twig', [
            'wiki' => $wiki,
            'form' => $form->createView(),
        ]);
    }

    protected function getWikiPermission(Wiki $wiki)
    {
        $wikiRoles = ['readRole' => false, 'writeRole' => false];
        $flag = false;

        if ($this->isGranted('ROLE_SUPERUSER')) {
            $wikiRoles['readRole'] = true;
            $wikiRoles['writeRole'] = true;
            $flag = true;
        } else {
            if (!empty($wiki->getReadRole())) {
                $readArray = explode(',', $wiki->getReadRole());
                array_walk($readArray, 'trim');

                foreach ($readArray as $read) {
                    if ($this->isGranted($read)) {
                        $wikiRoles['readRole'] = true;
                        $flag = true;
                    }
                }
            }

            if (!empty($wiki->getWriteRole())) {
                $writeArray = explode(',', $wiki->getWriteRole());
                array_walk($writeArray, 'trim');

                foreach ($writeArray as $write) {
                    if ($this->isGranted($write)) {
                        $flag = true;
                        $wikiRoles['writeRole'] = true;
                    }
                }
            }
        }

        return  $flag ? $wikiRoles : false;
    }
}
