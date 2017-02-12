<?php

namespace JV\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="jv_application")
 * @ORM\Entity(repositoryClass="JV\BlogBundle\Repository\ApplicationRepository")
 */
class Application
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @ORM\Column(name="date", type="datetimetz")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="JV\BlogBundle\Entity\Article", inversedBy="applications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $article;

    public function __construct()
    {
      $this->date = new \Datetime();
    }

    /**
     * @ORM\PrePersist
     */
    public function increase()
    {
      $this->getArticle()->increaseApplication();
    }

    /**
     * @ORM\PreRemove
     */
    public function decrease()
    {
      $this->getArticle()->decreaseApplication();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $author
     */
    public function setAuthor($author)
    {
      $this->author = $author;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
      return $this->author;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
      $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
      return $this->content;
    }

    /**
     * @param \Datetime $date
     */
    public function setDate(\Datetime $date)
    {
      $this->date = $date;
    }

    /**
     * @return \Datetime
     */
    public function getDate()
    {
      return $this->date;
    }

    /**
     * @param Article $article
     */
    public function setArticle(Article $article)
    {
      $this->article = $article;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
      return $this->article;
    }
}
