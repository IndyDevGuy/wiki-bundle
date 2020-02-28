<?php
namespace IndyDevGuy\WikiBundle\Controller;

use IndyDevGuy\WikiBundle\Entity\Wiki;
use IndyDevGuy\WikiBundle\Form\WikiType;
use IndyDevGuy\WikiBundle\Services\WikiEventService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 * @Route("/wiki")
 */
class WikiController extends WikiBaseController
{
    /**
     * @Route("/", name="wiki_index", methods="GET")
     * @Security("has_role('ROLE_SUPERUSER') || has_role('ROLE_ADMIN') || has_role('ROLE_WIKI') ")
     */
    public function indexAction(): Response
    {
        $wikis = $this->get('IndyDevGuy\WikiBundle\Repository\WikiRepository')->findAll();
        $this->pageTitle = 'Wiki List';
        $wikiArray = [];
        foreach ($wikis as $wiki) {
            if ($wikiRoles = $this->getWikiPermission($wiki)) {
                if ($wikiRoles['readRole']) {
                    $wikiArray[] = $wiki;
                }
            }
        }
        $this->get('twig')->addGlobal('pageTitle', $this->pageTitle);

        return $this->render(
            '@Wiki/wiki/index.html.twig',
            [
                'wikis' => $wikiArray,
                'pageTitle' => $this->pageTitle
            ]
        );
    }

    /**
     * @Security("has_role('ROLE_SUPERUSER') || has_role('ROLE_ADMIN') || has_role('ROLE_WIKI') ")
     * @Route("/add", name="wiki_add", methods="GET|POST")
     */
    public function AddAction(Request $request): Response
    {
        $wiki = new Wiki();

        return $this->getEditForm($request, $wiki, $this->get('IndyDevGuy\WikiBundle\Services\WikiEventService'));
    }

    /**
     * @Security("has_role('ROLE_SUPERUSER') || has_role('ROLE_ADMIN') || has_role('ROLE_WIKI') ")
     * @Route("/{wikiName}/edit", name="wiki_edit", methods="GET|POST")
     * @ParamConverter("wiki", options={"mapping"={"wikiName"="name"}})
     */
    public function editAction(Request $request, Wiki $wiki): Response
    {
        return $this->getEditForm($request, $wiki, $this->get('IndyDevGuy\WikiBundle\Services\WikiEventService'));
    }

    /**
     * @Security("has_role('ROLE_SUPERUSER') || has_role('ROLE_ADMIN') || has_role('ROLE_WIKI') ")
     * @Route("/{wikiName}/delete", name="wiki_delete", methods="GET")
     * @ParamConverter("wiki", options={"mapping"={"wikiName"="name"}})
     */
    public function deleteAction(Request $request, Wiki $wiki): Response
    {
        if (count($wiki->getWikiPages())) {
            $this->addFlash('error', 'The wiki cannot be deleted because of having a wiki-page.');
        } else {
            $this->get('IndyDevGuy\WikiBundle\Services\WikiEventService')
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

        if($add)
        {
            $this->pageTitle = 'Add New Wiki';
        }
        else
        {
            $this->pageTitle = 'Edit Wiki '.$wiki->getName();
        }
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
        $twig = $this->get('twig');
        $twig->addGlobal('pageTitle', $this->pageTitle);
        return $this->render('@Wiki/wiki/edit.html.twig', [
            'wiki' => $wiki,
            'form' => $form->createView(),
            'pageTitle' => $this->pageTitle
        ]);
    }

}
