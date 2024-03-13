<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Cat;
use App\Entity\Tag;
use App\Entity\Post;
use App\Entity\PostTags;

class PostTagsModel
{
    private ?int $id = null;
    private ?Tag $tag = null;
    private ?Cat $cat = null;
    private ?Post $post = null;
    private ?PostTags $postTags = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getTag(): ?Tag
    {
        return $this->tag;
    }

    public function setTag(?Tag $tag): static
    {
        $this->tag = $tag;
        return $this;
    }

    public function getCat(): ?Post
    {
        return $this->cat;
    }

    public function setCat(?Cat $cat): static
    {
        $this->cat = $cat;
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
