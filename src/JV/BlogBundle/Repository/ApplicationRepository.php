<?php

namespace JV\BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ApplicationRepository extends EntityRepository
{
  public function getApplicationsWithArticle($limit)
  {
    $qb = $this->createQueryBuilder('a');

    $qb
      ->innerJoin('a.article', 'art')
      ->addSelect('art')
    ;

    $qb->setMaxResults($limit);

    return $qb
      ->getQuery()
      ->getResult()
    ;
  }
}
