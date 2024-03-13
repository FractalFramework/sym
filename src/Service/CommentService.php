<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Mapper\CommentMapper;
use App\Entity\Post;

class CommentService
{
    public const PAGINATOR_PER_PAGE = 10;

    public function __construct(
        private CommentRepository $commentRepository,
        private EntityManagerInterface $manager,
        private CommentMapper $commentMapper,
        private PostRepository $postRepository,
    ) {
    }

    public function getCommentsPaginator(Post $post, int $offset): array
    {
        $commentsEntities = $this->commentRepository->getCommentsPaginator($post, $offset, self::PAGINATOR_PER_PAGE)->getQuery()->getResult();
        return $this->commentMapper->EntitiesToModels($commentsEntities);
    }

    public function getPaginationArrayButtons(int $nbOfPages): array
    {
        return array_map(fn($i = 0): int => (int) self::PAGINATOR_PER_PAGE * $i++, range(0, $nbOfPages - 1));
    }

    public function getNumberOfCommentsByPosts(Post $post): int
    {
        return $this->commentRepository->countByPosts($post);
    }

    public function saveComment($form, Post $post, User $user): void
    {
        $commentModel = new Comment();
        $commentModel->setPost($post);
        $commentModel->setUser($user);
        $commentModel->setDate(new \DateTime());
        $commentModel->setContent($form->get("content")->getData());
        $commentModel->setStatus(1); //by default
        $this->commentRepository->saveComment($commentModel);
    }

    //admin

    public function getAllComments(): array
    {
        $commentModel = $this->commentRepository->findAll();
        return $this->commentMapper->EntitiesToModels($commentModel);
    }

    public function updateStatus(int $id): void
    {
        $comment = $this->commentRepository->findOneById($id);
        $status = $comment->getStatus();
        $status = $status == 1 ? 0 : 1;
        $comment->setStatus($status);
        $this->commentRepository->saveComment($comment);
    }

}
