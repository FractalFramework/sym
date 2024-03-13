<?php

declare(strict_types=1);

namespace App\Mapper;

use Doctrine\Common\Collections\Collection;
use App\Model\CatModel;

class CatMapper
{
    public function EntityToModel(object $catEntity): CatModel
    {
        $catModel = new CatModel();
        $catModel->setId($catEntity->getId());
        $catModel->setName($catEntity->getName());
        $catModel->setTags($catEntity->getTags());
        return $catModel;
    }

    public function EntitiesToModels(array $catEntities): array
    {
        $catModels = [];
        foreach ($catEntities as $catEntity) {
            $catModels[] = $this->EntityToModel($catEntity);
        }
        return $catModels;
    }

}
