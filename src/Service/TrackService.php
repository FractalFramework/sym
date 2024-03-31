<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Track;
use App\Repository\TrackRepository;
use App\Repository\PostRepository;
use App\Mapper\TrackMapper;
use App\Entity\Post;

class TrackService
{
    public const PAGINATOR_PER_PAGE = 10;

    public function __construct(
        private TrackRepository $trackRepository,
        private EntityManagerInterface $manager,
        private TrackMapper $trackMapper,
        private PostRepository $postRepository,
    ) {
    }

    public function getTracksPaginator(Post $post, int $offset): array
    {
        $tracksEntities = $this->trackRepository->getTracksPaginator($post, $offset, self::PAGINATOR_PER_PAGE)->getQuery()->getResult();
        return $this->trackMapper->EntitiesToModels($tracksEntities);
    }

    public function getPaginationArrayButtons(int $nbOfPages): array
    {
        return array_map(fn($i = 0): int => (int) self::PAGINATOR_PER_PAGE * $i++, range(0, $nbOfPages - 1));
    }

    public function getNumberOfTracksByPosts(Post $post): int
    {
        return $this->trackRepository->countByPosts($post);
    }

    public function saveTrack($form, Post $post, User $user): void
    {
        $trackModel = new Track();
        $trackModel->setPost($post);
        $trackModel->setUser($user);
        $trackModel->setDate(new \DateTime());
        $trackModel->setContent($form->get("content")->getData());
        $trackModel->setStatus(1); //by default
        $this->trackRepository->saveTrack($trackModel);
    }

    //admin

    public function getAllTracks(): array
    {
        $trackModel = $this->trackRepository->findAll();
        return $this->trackMapper->EntitiesToModels($trackModel);
    }

    public function updateStatus(int $id): void
    {
        $track = $this->trackRepository->findOneById($id);
        $status = $track->getStatus();
        $status = $status == 1 ? 0 : 1;
        $track->setStatus($status);
        $this->trackRepository->saveTrack($track);
    }

}
