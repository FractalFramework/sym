<?php

declare(strict_types=1);

namespace App\Mapper;

use Doctrine\Common\Collections\Collection;
use App\Model\TrackModel;

class TrackMapper
{

    public function EntityToModel(object $trackEntity): TrackModel
    {
        $trackModel = new TrackModel();
        $trackModel->setId($trackEntity->getId());
        $trackModel->setUsername($trackEntity->getUser()->getUser());
        $trackModel->setDate($trackEntity->getDate());
        $trackModel->setContent($trackEntity->getContent());
        $trackModel->setStatus($trackEntity->getStatus());
        $trackModel->setPostSlug($trackEntity->getPost()->getSlug());
        $trackModel->setPostTitle($trackEntity->getPost()->getTitle());
        $trackModel->setAvatar('/public/avatars/' . $trackEntity->getUser()->getAvatar());
        return $trackModel;
    }

    public function EntitiesToModels(array $trackEntities): array
    {
        $trackModels = [];
        foreach ($trackEntities as $trackEntity) {
            $trackModels[] = $this->EntityToModel($trackEntity);
        }
        return $trackModels;
    }

}
