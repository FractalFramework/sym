<?php

namespace App\Controller;

use App\Form\AvatarFormType;
use App\Service\UserService;
use App\Service\PostService;
use App\Service\CommentService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    public function __construct(
        private Security $security,
        private UserService $userService,
        private PostService $postService,
        private CommentService $commentService,
    ) {
    }

    #[Route('/admin/posts/', name: 'admin_posts')]
    #[Route('/admin/posts/{id}', name: 'admin_postsId')]
    public function showPosts(int $id = null): Response //, Request $request, EntityManagerInterface $manager
    {
        if ($id && $this->security->isGranted('ROLE_USER')) {
            $this->postService->updateStatus($id);
        }
        $postsModel = $this->postService->getAllPosts();

        return $this->render(
            'admin/posts.html.twig',
            [
                'posts' => $postsModel,
                'currentUser' => $this->getUser(),
            ]
        );
    }

    #[Route('/admin/comments', name: 'admin_comments')]
    #[Route('/admin/comments/{id}', name: 'admin_commentsId')]
    public function showComments(int $id = null): Response
    {
        $userConnected = $this->getUser();
        if ($id && $this->security->isGranted('ROLE_ADMIN')) {
            $this->commentService->updateStatus($id);
            return $this->redirectToRoute('admin_comments');
        }
        $commentsModel = $this->commentService->getAllComments();

        return $this->render(
            'admin/comments.html.twig',
            [
                'comments' => $commentsModel,
                'currentUser' => $this->getUser(),
            ]
        );
    }

    //avatar
    #[Route('/avatar/{avatar}', name: 'admin_avatar_select')]
    public function avatar(int $avatar): Response
    {
        $this->userService->saveAvatar(
            $this->getUser(),
            $avatar,
        );
        return $this->redirectToRoute('app_user');
    }

    #[Route('/avatar', name: 'admin_avatar')]
    public function userAvatar(Request $request): Response
    {
        $formUser = $this->createForm(AvatarFormType::class);
        $avatars = $this->userService->getAvatars();
        $formUser->handleRequest($request);

        //update
        if ($formUser->isSubmitted() && $formUser->isValid()) {
            $this->userService->saveAvatar(
                $this->getUser(),
                $formUser->get('avatar')->getData(),
            );
        }
        return $this->render('admin/avatar.html.twig', [
            'formUser' => $formUser->createView(),
            'avatars' => $avatars,
        ]);
    }

    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
