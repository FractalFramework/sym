<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\PostTags;

class PostTagsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostTags::class);
    }

    public function savePostTags(PostTags $postTag): void
    {
        $this->getEntityManager()->persist($postTag);
        $this->getEntityManager()->flush();
    }

    public function delete(PostTags $postTag): void
    {
        $this->getEntityManager()->remove($postTag);
        $this->getEntityManager()->flush();
    }

}
