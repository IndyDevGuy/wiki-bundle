<?php

namespace IndyDevGuy\WikiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="IndyDevGuy\WikiBundle\Repository\WikiRepository")
 */
class Wiki
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="IndyDevGuy\WikiBundle\Entity\WikiPage", mappedBy="wiki")
     */
    private $wikiPages;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $read_role;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $write_role;

    public function __construct()
    {
        $this->wikiPages = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|WikiPage[]
     */
    public function getWikiPages()
    {
        return $this->wikiPages;
    }

    public function addWikiPage(WikiPage $wikiPage)
    {
        if (!$this->wikiPages->contains($wikiPage)) {
            $this->wikiPages[] = $wikiPage;
            $wikiPage->setWiki($this);
        }

        return $this;
    }

    public function removeWikiPage(WikiPage $wikiPage)
    {
        if ($this->wikiPages->contains($wikiPage)) {
            $this->wikiPages->removeElement($wikiPage);
            // set the owning side to null (unless already changed)
            if ($wikiPage->getWiki() === $this) {
                $wikiPage->setWiki(null);
            }
        }

        return $this;
    }

    public function setReadRole(?string $read_role)
    {
        $this->read_role = $read_role;

        return $this;
    }

    public function getReadRole()
    {
        return $this->read_role;
    }

    public function setWriteRole(?string $write_role)
    {
        $this->write_role = $write_role;

        return $this;
    }

    public function getWriteRole()
    {
        return $this->write_role;
    }
}
