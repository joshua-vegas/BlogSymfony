<?php

namespace JV\BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ArticleRepository extends \Doctrine\ORM\EntityRepository
{
  public function getArticlesBefore(\Datetime $date)
  {
    return $this->createQueryBuilder('a')
      ->where('a.updatedAt <= :date')
      ->orWhere('a.updatedAt IS NULL AND a.date <= :date')
      ->andWhere('a.applications IS EMPTY')
      ->setParameter('date', $date)
      ->getQuery()
      ->getResult()
      ;
  }

  public function getArticles($page, $nbPerPage)
  {
    $query = $this->createQueryBuilder('a')
      ->leftJoin('a.categories', 'c')
      ->addSelect('c')
      ->orderBy('a.date', 'DESC')
      ->getQuery()
    ;

    $query
      ->setFirstResult(($page-1) * $nbPerPage)
      ->setMaxResults($nbPerPage)
    ;

    return new Paginator($query, true);
  }

  public function myFindAll()
  {
    $queryBuilder = $this->createQueryBuilder('a');

    $query = $queryBuilder->getQuery();

    $results = $query->getResult();

    return $results;
  }

  public function myFind()
  {
    $qb = $this->createQueryBuilder('a');

    $qb
      ->where('a.author = :author')
      ->setParameter('author', 'Bob')
    ;

    $this->whereCurrentYear($qb);

    $qb->orderBy('a.date', 'DESC');

    return $qb
      ->getQuery()
      ->getResult()
    ;
  }

  public function getArticleWithCategories(array $categoryNames)
  {
    $qb = $this->createQueryBuilder('a');

    $qb
      ->innerJoin('a.categories', 'c')
      ->addSelect('c')
    ;

    $qb->where($qb->expr()->in('c.name', $categoryNames));

    return $qb
      ->getQuery()
      ->getResult()
      ;
  }

  protected function whereCurrentYear(QueryBuilder $qb)
  {
    $qb
      ->andWhere('a.date BETWEEN :start AND :end')
      ->setParameter('start', new \Datetime(date('Y') . '-01-01'))
      ->setParameter('end', new \Datetime(date('Y') . '-12-31'))
    ;
  }
}
