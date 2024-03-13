<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use App\Repository\CatRepository;
use App\Repository\PostTagsRepository;
use App\Mapper\PostTagsMapper;
use App\Entity\PostTags;
use App\Entity\Post;

class PostTagsService
{

    public function __construct(
        private PostRepository $PostRepo,
        private TagRepository $tagRepo,
        private CatRepository $catRepo,
        private PostTagsRepository $postTagsRepo,
        private PostTagsMapper $postTagsMapper,
        private EntityManagerInterface $manager
    ) {

    }

    public function savePostTag(
        Post $post,
        string $tagId,
    ): void {
        $postTags = new PostTags();
        $tag = $this->tagRepo->findOneBy(['id' => $tagId]);
        if ($tag) {
            $cat = $tag->getCat();
            $postTags->setPost($post);
            $postTags->setCat($cat);
            $postTags->setTag($tag);
            $this->postTagsRepo->savePostTags($postTags);
        }
    }

    public function getPostsByTag(int $id): Post|array
    {
        $postTagsModel = $this->postTagsRepo->findBy(['tag' => $id], ['id' => 'ASC']);
        return $this->postTagsMapper->EntitiesArrayToModels($postTagsModel);
    }

}
