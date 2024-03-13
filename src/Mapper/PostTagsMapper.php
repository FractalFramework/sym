<?php

declare(strict_types=1);

namespace App\Mapper;

use Doctrine\Common\Collections\Collection;
use App\Model\PostTagsModel;

class PostTagsMapper
{

    public function EntityToModel(object $postTags): PostTagsModel
    {
        $postTagsModel = new PostTagsModel();
        $postTagsModel->setId($postTags->getId());
        $postTagsModel->setTag($postTags->getTag());
        $postTagsModel->setCat($postTags->getTag()->getCat());
        $postTagsModel->setPost($postTags->getPost());
        return $postTagsModel;
    }

    public function EntitiesToModels(Collection $postTagsEntities): array
    {
        $postTagsModels = [];
        foreach ($postTagsEntities as $postTags) {
            $postTagsModels[] = $this->EntityToModel($postTags);
        }
        return $postTagsModels;
    }

    public function EntitiesArrayToModels(array $postTagsEntities): array
    {
        $postTagsModels = [];
        foreach ($postTagsEntities as $postTags) {
            $postTagsModels[] = $this->EntityToModel($postTags);
        }
        return $postTagsModels;
    }

}
