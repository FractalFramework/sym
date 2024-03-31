<?php

namespace App\Controller;

use App\Form\AvatarFormType;
use App\Service\UserService;
use App\Service\PostService;
use App\Service\TrackService;
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
        private TrackService $trackService,
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

    #[Route('/admin/tracks', name: 'admin_tracks')]
    #[Route('/admin/tracks/{id}', name: 'admin_tracksId')]
    public function showTracks(int $id = null): Response
    {
        $userConnected = $this->getUser();
        if ($id && $this->security->isGranted('ROLE_ADMIN')) {
            $this->trackService->updateStatus($id);
            return $this->redirectToRoute('admin_tracks');
        }
        $tracksModel = $this->trackService->getAllTracks();

        return $this->render(
            'admin/tracks.html.twig',
            [
                'tracks' => $tracksModel,
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
