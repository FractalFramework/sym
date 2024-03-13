<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Model\MediaModel;
use App\Service\MediaService;
use Doctrine\Common\Collections\Collection;

class MediaMapper
{
    public function __construct(
        private readonly MediaService $mediaService,
    ) {

    }

    public function EntityToModel(object $mediaEntity): MediaModel
    {
        $mediaModel = new MediaModel();
        $mediaModel->setId($mediaEntity->getId());
        $mediaModel->setFilename($this->mediaService->goodUrl($mediaEntity->getFilename()));
        $mediaModel->setMediaType($mediaEntity->getType());
        $mediaModel->setPost($mediaEntity->getPost());
        return $mediaModel;
    }

    public function EntitiesToModels(Collection $mediaEntities): array
    {
        $mediaModels = [];
        foreach ($mediaEntities as $mediaEntity) {
            $mediaModels[] = $this->EntityToModel($mediaEntity);
        }
        return $mediaModels;
    }

}
