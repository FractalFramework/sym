<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Mapper\CommentMapper;
use App\Mapper\MediaMapper;
use App\Mapper\PostTagsMapper;
use App\Mapper\TagMapper;
use App\Model\PostModel;
use App\Entity\Post;

class PostMapper
{
    public function __construct(
        private CommentMapper $commentMapper,
        private PostTagsMapper $postTagsMapper,
        private MediaMapper $mediaMapper,
        private TagMapper $tagMapper,
    ) {
    }

    public function EntityToModel(Post $postEntity): PostModel
    {
        $postModel = new PostModel();
        $postModel->setId($postEntity->getId());
        $postModel->setUsername($postEntity->getUser()->getUsername());
        $postModel->setCreatedAt($postEntity->getCreatedAt());
        $postModel->setUpdatedAt($postEntity->getUpdatedAt());
        $postModel->setTitle($postEntity->getTitle());
        $postModel->setSlug($postEntity->getSlug());
        $postModel->setImage('/uploads/' . $postEntity->getImage());
        $postModel->setStatus($postEntity->getStatus());
        $postModel->setContent($postEntity->getContent());
        $postModel->setMedia($this->mediaMapper->EntitiesToModels($postEntity->getMedia()));
        $postModel->setPostTags($this->postTagsMapper->EntitiesToModels($postEntity->getPostTags()));
        return $postModel;
    }

    public function EntitiesToModels(array $postEntities): array
    {
        $postModels = [];
        foreach ($postEntities as $postEntity) {
            $postModels[] = $this->EntityToModel($postEntity);
        }
        return $postModels;
    }

}
