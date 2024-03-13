<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use App\Model\PostTagsModel;
use App\Mapper\PostTagsMapper;
use App\Service\PostService;
use App\Service\PostTagsService;
use App\Entity\Tag;

class PostTagsController extends AbstractController
{

    public function __construct(
        private PostService $postService,
        private PostTagsService $postTagsService,
        private PostTagsModel $postTagsModel,
        private PostTagsMapper $postTagsMapper,
    ) {

    }

    //display posts by tag
    #[Route('/tag/{id}', name: 'show_tag')]
    public function show(Tag $tag = null, int $id): Response
    {
        $postTagsModel = $this->postTagsService->getPostsByTag($id);
        $posts = new ArrayCollection;
        if ($postTagsModel) {
            foreach ($postTagsModel as $postTagsModel) {
                $postId = $postTagsModel->getPost()->getId();
                $posts->add($this->postService->getById($postId));
            }
        }
        $currentTag = $postTagsModel->getTag()->getName();

        return $this->render('home/tagPosts.html.twig', [
            'pageTitle' => 'Posts avec le tag ' . $currentTag,
            'minRoleToEdit' => 'ROLE_USER',
            'posts' => $posts,
            'tag' => $currentTag,
        ]);
    }

    //edit
    #[Route('/tag/{id}/edit', name: 'edit_tag')]
    public function form(Tag $tag = null, int $id, Request $request): Response
    {
        $posts = $this->postTagsService->getPostsByTag($id);

        return $this->render('home/tagPostEdit.html.twig', [
            'posts' => $posts,
        ]);
    }
}
