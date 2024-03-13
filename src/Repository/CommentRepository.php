<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;

class CommentRepository extends ServiceEntityRepository
{

    public function __construct(
        ManagerRegistry $registry,
        private EntityManagerInterface $manager
    ) {
        parent::__construct($registry, Comment::class);
    }

    public function getCommentsPaginator(Post $post, int $offset, int $maxResults): Paginator
    {
        $query = $this->createQueryBuilder('c')
            ->andWhere('c.post = :post')
            ->setParameter('post', $post)
            ->andWhere('c.status = 1')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults($maxResults)
            ->setFirstResult($offset)
            ->getQuery()
        ;
        return new Paginator($query);
    }

    public function countByPosts(Post $post): int
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->andWhere('c.post = :post')
            ->setParameter('post', $post)
            ->andWhere('c.status = 1')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    //admin

    public function findAll(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function saveComment(Comment $comment): void
    {
        $this->getEntityManager()->persist($comment);
        $this->getEntityManager()->flush();
    }

}
