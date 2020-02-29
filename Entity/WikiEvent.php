<?php

namespace IndyDevGuy\WikiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="IndyDevGuy\WikiBundle\Repository\WikiEventRepository")
 */
class WikiEvent
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $created_by;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $wiki_page_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $data;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $wiki_id;

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt(int $created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCreatedBy()
    {
        return $this->created_by;
    }

    public function setCreatedBy(string $created_by)
    {
        $this->created_by = $created_by;

        return $this;
    }

    public function getWikiPageId()
    {
        return $this->wiki_page_id;
    }

    public function setWikiPageId(int $wiki_page_id)
    {
        $this->wiki_page_id = $wiki_page_id;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData(string $data)
    {
        $this->data = $data;

        return $this;
    }

    public function getWikiId()
    {
        return $this->wiki_id;
    }

    public function setWikiId(int $wiki_id)
    {
        $this->wiki_id = $wiki_id;

        return $this;
    }
}
