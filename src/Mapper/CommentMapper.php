<?php

declare(strict_types=1);

namespace App\Mapper;

use Doctrine\Common\Collections\Collection;
use App\Model\CommentModel;

class CommentMapper
{

    public function EntityToModel(object $commentEntity): CommentModel
    {
        $commentModel = new CommentModel();
        $commentModel->setId($commentEntity->getId());
        $commentModel->setUsername($commentEntity->getUser()->getUser());
        $commentModel->setDate($commentEntity->getDate());
        $commentModel->setContent($commentEntity->getContent());
        $commentModel->setStatus($commentEntity->getStatus());
        $commentModel->setPostSlug($commentEntity->getPost()->getSlug());
        $commentModel->setPostTitle($commentEntity->getPost()->getTitle());
        $commentModel->setAvatar('/avatars/' . $commentEntity->getUser()->getAvatar());
        return $commentModel;
    }

    public function EntitiesToModels(array $commentEntities): array
    {
        $commentModels = [];
        foreach ($commentEntities as $commentEntity) {
            $commentModels[] = $this->EntityToModel($commentEntity);
        }
        return $commentModels;
    }

}
