<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\PostTags;
use App\Entity\Post;
use App\Entity\Cat;

class TagModel
{
    private ?int $id = null;
    private ?string $name = null;
    private ?PostTags $postTags = null;
    private ?Post $post = null;
    private ?Cat $cat = null;

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

    public function getCat(): ?Cat
    {
        return $this->cat;
    }

    public function setCat(?Cat $cat): static
    {
        $this->cat = $cat;
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
