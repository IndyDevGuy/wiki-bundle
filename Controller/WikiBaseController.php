<?php
namespace IndyDevGuy\Bundle\WikiBundle\Controller;

use IndyDevGuy\Bundle\WikiBundle\Entity\Wiki;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WikiBaseController extends AbstractController
{
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