<?php

declare(strict_types=1);

namespace App\Mapper;

use Doctrine\Common\Collections\Collection;
use App\Model\TagModel;

class TagMapper
{
    public function EntityToModel(object $tagEntity): TagModel
    {
        $tagModel = new TagModel();
        $tagModel->setId($tagEntity->getId());
        $tagModel->setName($tagEntity->getName());
        $tagModel->setPost($tagEntity->getPost());
        $tagModel->setCat($tagEntity->getCat());
        return $tagModel;
    }

    public function EntitiesToModels(array $tagEntities): array
    {
        $tagModels = [];
        foreach ($tagEntities as $tagEntity) {
            $tagModels[] = $this->EntityToModel($tagEntity);
        }
        return $tagModels;
    }

}
