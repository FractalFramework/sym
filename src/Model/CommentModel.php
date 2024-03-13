<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;
use App\Entity\User;
use App\Entity\Post;

class CommentModel
{
    private ?int $id = null;
    private ?Post $post = null;
    private ?User $user = null;
    private ?string $username = null;
    private ?string $content = null;
    private ?DateTime $date = null;
    private ?int $status = null;
    private ?string $postSlug = null;
    private ?string $postTitle = null;
    private ?string $avatar = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function getPostSlug(): ?string
    {
        return $this->postSlug;
    }

    public function setPostSlug(?string $postSlug): static
    {
        $this->postSlug = $postSlug;
        return $this;
    }

    public function getPostTitle(): ?string
    {
        return $this->postTitle;
    }

    public function setPostTitle(?string $postTitle): static
    {
        $this->postTitle = $postTitle;
        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

}
