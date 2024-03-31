<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Mapper\TrackMapper;
use App\Model\UserModel;
use App\Entity\User;

class UserMapper
{

    public function __construct(
        private TrackMapper $trackMapper
    ) {

    }

    public function EntityToModel(User $userEntity): UserModel
    {
        $userModel = new UserModel();
        $userModel->setId($userEntity->getId());
        $userModel->setUsername($userEntity->getUsername());
        $userModel->setEmail($userEntity->getEmail());
        $userModel->setPassword($userEntity->getPassword());
        $userModel->setRole($userEntity->getRoles());
        $userModel->setAvatar($userEntity->getAvatar());
        return $userModel;
    }

}
