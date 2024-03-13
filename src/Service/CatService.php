<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;
use App\Repository\PostRepository;
use App\Repository\CatRepository;
use App\Mapper\CatMapper;
use App\Entity\Post;

class CatService
{

    public function __construct(
        private PostRepository $postRepo,
        private CatRepository $catRepo,
        private CatMapper $catMapper,
        private EntityManagerInterface $manager
    ) {

    }

    public function getAll(): Collection|array
    {
        $catModel = $this->catRepo->findAll();
        return $this->catMapper->EntitiesToModels($catModel);
    }

}
