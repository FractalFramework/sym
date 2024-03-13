<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Model\UserModel;
use App\Mapper\UserMapper;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly TokenGeneratorInterface $tokenGenerator,
        private readonly ParameterBagInterface $parameterBag,
        private readonly JwtService $JwtService,
        private UserRepository $userRepository,
        private UserModel $userModel,
        private UserMapper $mapper,
    ) {
    }

    public function getAll(): User|array
    {
        return $this->userRepository->findAll();
    }

    public function getById(int $id): UserModel
    {
        $userEntity = $this->userRepository->findOneById($id);
        return $this->mapper->EntityToModel($userEntity);
    }

    public function saveUser(User $user, string $plainPassword): void
    {
        $password = $this->userPasswordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($password);
        $user->setRoles(['ROLE_EDIT']);
        $user->setAvatar($this->chooseRandomAvatar());
        $this->userRepository->saveUser($user);
    }

    public function deleteUser(User $user): bool
    {
        if ($this->userRepository->findOneById($user->getId()) === null) {
            return false;
        }
        $this->userRepository->delete($user);
        return true;
    }

    //avatar
    public function getAvatars(): array
    {
        $dir = 'avatars/';
        $images = scandir($dir);
        unset($images[0]);
        unset($images[1]);
        sort($images);
        $images = array_map(fn($image) => $image, $images);
        return $images;
    }

    public function chooseAvatar(): array
    {
        return array_map(fn($avatar) => substr(strchr($avatar, '/'), 1, -4), $this->getAvatars());
    }

    public function chooseRandomAvatar(): string
    {
        $avatars = $this->getAvatars();
        $numberOfFiles = count($avatars);
        return $avatars[mt_rand(0, $numberOfFiles - 1)];
    }

    public function saveAvatar(User $user, string $avatarKey): void
    {
        $avatars = $this->getAvatars();
        $avatar = $avatars[$avatarKey] ?? '';
        if ($avatar) {
            $user->setAvatar($avatar);
            $this->userRepository->saveUser($user);
        }
    }

    public function isUserVerifiedYet(User $user): bool
    {
        return $user->isVerified();
    }

    public function newRegisterToken(UserModel $user): string
    {
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];
        $payload = [
            'userId' => $user->getId()
        ];
        $param = $this->parameterBag->get('app.jwtsecret');
        return $this->JwtService->generate($header, $payload, $param);
    }

    public function getUserModel(User $user): UserModel
    {
        $this->userModel->setId($user->getId());
        $this->userModel->setUsername($user->getUsername());
        $this->userModel->setEmail($user->getEmail());
        return $this->userModel;
    }

}
