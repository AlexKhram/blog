<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Class PostRepository
 * @package App\Entity
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findLatest(int $page = 1): Pagerfanta
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT p, t
                FROM App:Post p
                LEFT JOIN p.tags t
                WHERE p.publishedAt <= :now
                ORDER BY p.publishedAt DESC
            ')
            ->setParameter('now', new \DateTime())
        ;
        return $this->createPaginator($query, $page);
    }

    private function createPaginator(Query $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(Post::NUM_ITEMS);
        $paginator->setCurrentPage($page);
        return $paginator;
    }
}
