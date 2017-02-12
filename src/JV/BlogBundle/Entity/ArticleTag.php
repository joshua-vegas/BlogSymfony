<?php

namespace JV\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="jv_article_tag")
 */
class ArticleTag
{
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\ManyToOne(targetEntity="JV\BlogBundle\Entity\Article")
   * @ORM\JoinColumn(nullable=false)
   */
  private $article;

  /**
   * @ORM\ManyToOne(targetEntity="JV\BlogBundle\Entity\Tag")
   * @ORM\JoinColumn(nullable=false)
   */
  private $tag;

  /**
   * @return integer
   */
  public function getId()
  {
    return $this->id;
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

  /**
   * @param Tag $tag
   */
  public function setTag(Tag $tag)
  {
    $this->tag = $tag;
  }

  /**
   * @return Tag
   */
  public function getTag()
  {
    return $this->tag;
  }
}
