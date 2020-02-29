<?php

namespace IndyDevGuy\WikiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="IndyDevGuy\WikiBundle\Repository\WikiPageRepository")
 */
class WikiPage
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="IndyDevGuy\WikiBundle\Entity\Wiki", inversedBy="wikiPages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $wiki;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $highlighttheme;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWiki()
    {
        return $this->wiki;
    }

    public function setWiki(Wiki $wiki)
    {
        $this->wiki = $wiki;

        return $this;
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

    public function getContent()
    {
        return $this->content;
    }

    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }

    public function getHighlighttheme()
    {
        return $this->highlighttheme;
    }

    public function setHighlighttheme(string $theme)
    {
        $this->highlighttheme = $theme;
    }
}
