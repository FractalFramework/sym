<?php

declare(strict_types=1);

namespace App\Service;

//use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\PostTagsRepository;
use App\Repository\PostRepository;
use App\Repository\MediaRepository;
use App\Service\MediaService;
use App\Mapper\PostMapper;
use App\Model\PostModel;
use App\Entity\Post;
use App\Entity\User;

class PostService
{

    public function __construct(
        //private readonly EntityManagerInterface $manager,
        private readonly SluggerInterface $slugger,
        private readonly PostTagsRepository $postTagsRepository,
        private readonly PostRepository $postRepository,
        private readonly MediaRepository $mediaRepository,
        private readonly MediaService $mediaService,
        private readonly PostMapper $postMapper,
    ) {

    }
    public const PAGINATOR_PER_PAGE = 10;

    public function getAll(): Post|array
    {
        return $this->postRepository->findAll();
    }

    public function getPostsPaginator(int $offset): array
    {
        $posts = $this->postRepository->getPostsPaginator($offset, self::PAGINATOR_PER_PAGE)->getQuery()->getResult();
        return $this->postMapper->EntitiesToModels($posts);
    }

    public function countPostPublished(): int
    {
        return $this->postRepository->countByStatus();
    }

    public function getPaginationArrayButtons(int $nbOfPages): array
    {
        return array_map(fn($i = 0): int => (int) self::PAGINATOR_PER_PAGE * $i++, range(0, $nbOfPages - 1));
    }

    public function getAllPosts(): array
    {
        $postModel = $this->postRepository->findAll();
        return $this->postMapper->EntitiesToModels($postModel);
    }

    public function getLastsPosts(): array
    {
        $postModel = $this->postRepository->findLastsByStatus();
        return $this->postMapper->EntitiesToModels($postModel);
    }

    public function getById(int $id): PostModel
    {
        $postModel = $this->postRepository->findOneById($id);
        return $this->postMapper->EntityToModel($postModel);
    }

    public function getBySlug(string $slug): PostModel
    {
        $postModel = $this->postRepository->findOneBySlug($slug);
        return $this->postMapper->EntityToModel($postModel);
    }

    public function savePost(
        Post $post,
        User $user,
        string $video = null,
    ): void {
        if (!$post->getId()) {
            $post->setUser($user);
            $post->setCreatedAt(new \DateTime());
            $post->setStatus(1);
        }
        $slug = $this->slugger->slug($post->getTitle());
        $post->setSlug($slug->toString());
        $post->setUpdatedAt(new \DateTime());
        $this->postRepository->savePost($post);
        if ($video) {
            $this->mediaService->saveMedia($post, $video);
        }
    }

    public function setAsFirstImage(Post $post, int $mediaId): void
    {
        $medias = $post->getMedia();
        foreach ($medias as $media) {
            if ($media->getId() == $mediaId) {
                $post->setImage($media->getFilename());
                $this->postRepository->savePost($post);
            }
        }
    }

    public function deleteTag(Post $post, int $tagId): void
    {
        $postTags = $post->getPostTags();
        foreach ($postTags as $postTag) {
            if ($postTag->getTag()->getId() == $tagId) {
                $postTag = $this->postTagsRepository->findOneById($postTag->getId());
                $this->postTagsRepository->delete($postTag);
            }
        }
    }

    public function deleteMedia(Post $post, int $tagId): void
    {
        $medias = $post->getMedia();
        //dd($postTags);
        foreach ($medias as $media) {
            if ($media->getId() == $tagId) {
                $media = $this->mediaRepository->findOneById($media->getId());
                $this->mediaRepository->delete($media);
            }
        }
    }

    public function updateStatus(int $id): void
    {
        $post = $this->postRepository->findOneById($id);
        $status = $post->getStatus();
        $status = $status == 1 ? 0 : 1;
        $post->setStatus($status);
        $this->postRepository->savePost($post);
    }

}
