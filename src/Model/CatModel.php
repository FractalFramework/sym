<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\Common\Collections\Collection;
use App\Entity\PostTags;
use App\Entity\Post;

class CatModel
{
    private ?int $id = null;
    private ?string $name = null;
    private ?PostTags $postTags = null;
    private ?Post $post = null;
    private Collection $tags;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
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

    public function getTags(): Collection
    {
        return $this->tags;
    }

    //added
    public function setTags(Collection $tags): CatModel
    {
        $this->tags = $tags;
        return $this;
    }

    public function getPostTags(): ?PostTags
    {
        return $this->postTags;
    }

    public function setPostTags(?PostTags $postTags): static
    {
        $this->postTags = $postTags;
        return $this;
    }

}
