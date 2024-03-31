<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\PostTagsService;
use App\Service\PostService;
use App\Service\TrackService;
use App\Service\MediaService;
use App\Service\FileUploader;
use App\Service\CatService;
use App\Model\TrackModel;
use App\Form\PostFormType;
use App\Form\PostTagsFormType;
use App\Form\TrackFormType;
use App\Entity\Post;

class PostController extends AbstractController
{

    public function __construct(
        private CatService $catService,
        private PostService $postService,
        private MediaService $mediaService,
        private TrackService $trackService,
        private PostTagsService $postTagsService,
        private PostFormType $postFormType,
        private TrackFormType $trackFormType,
        private PostTagsFormType $postTagsFormType,
        private SluggerInterface $slugger,
    ) {

    }

    //edit
    #[Route('/post/{id}/edit', name: 'edit_post')]
    public function formEdit(Post $post = null, int $id, Request $request, FileUploader $fileUploader): Response
    {
        $formPost = $this->createForm(PostFormType::class, $post);
        $formPost->handleRequest($request);

        //update
        if ($formPost->isSubmitted() && $formPost->isValid()) {

            //post
            $this->postService->savePost(
                $post,
                $this->getUser(),
                $formPost->get('video')->getData(),
            );

            //medias
            $mediaFiles = $formPost->get('media')->getData();
            if ($mediaFiles) {
                foreach ($mediaFiles as $mediaFile) {
                    $mediaFileName = $fileUploader->upload($mediaFile);
                    $this->mediaService->saveMedia(
                        $post,
                        $mediaFileName,
                    );
                    $this->addFlash(
                        'updated',
                        'Le média ' . $mediaFileName . ' a été ajouté au catalogue.'
                    );
                }
            }

            $this->addFlash(
                'updated',
                'Les modifications ont "été prises en compte.'
            );

        }

        //tags
        $formTags = $this->createForm(PostTagsFormType::class);
        $catsModel = $this->catService->getAll();
        $formTags->handleRequest($request);
        $tagId = $formTags->get('tagId')->getData();
        if ($formTags->isSubmitted() && $formTags->isValid() && $tagId) {
            $this->postTagsService->savePostTag(
                $post,
                $formTags->get('tagId')->getData(),
            );
            return $this->redirectToRoute('edit_post', [
                'id' => $id,
            ]);
        }
        //render
        $postModel = $this->postService->getById($id);
        return $this->render('home/postEdit.html.twig', [
            'formPost' => $formPost->createView(),
            'formTags' => $formTags->createView(),
            'post' => $postModel,
            'cats' => $catsModel,
            'user' => $this->getUser(),
        ]);
    }

    //edit
    #[Route('/post/new', name: 'new_post')]
    public function form(Post $post = null, Request $request, FileUploader $fileUploader): Response
    {
        $post = new Post();

        $formPost = $this->createForm(PostFormType::class, $post);
        $formPost->handleRequest($request);

        //update
        if ($formPost->isSubmitted() && $formPost->isValid()) {

            //post
            $this->postService->savePost(
                $post,
                $this->getUser(),
                $formPost->get('video')->getData(),
            );

            //medias
            $mediaFiles = $formPost->get('media')->getData();
            if ($mediaFiles) {
                foreach ($mediaFiles as $mediaFile) {
                    $mediaFileName = $fileUploader->upload($mediaFile);
                    $this->mediaService->saveMedia(
                        $post,
                        $mediaFileName,
                        'image'
                    );
                    $this->addFlash(
                        'updated',
                        'L`\'image ' . $mediaFileName . ' a été ajoutée au catalogue.'
                    );
                }
            }

            $this->addFlash(
                'updated',
                'Le nouveau Post a été enregistré. Il reste à ajouter une image de garde, et des tags.'
            );
            return $this->redirectToRoute('edit_post', [
                'id' => $post->getId(),
            ]);

        }

        //render
        return $this->render('home/postNew.html.twig', [
            'formPost' => $formPost->createView(),
            'post' => $post,
            'user' => $this->getUser(),
        ]);
    }

    //delete tag
    #[Route('/post/{id}/deltag/{tagId}', name: 'del_tag')]
    public function delTag(Post $post = null, int $tagId): Response
    {
        $this->postService->deleteTag($post, $tagId);
        return $this->redirectToRoute('edit_post', ['id' => $post->getId()]);
    }

    //delete media
    #[Route('/post/{id}/delmedia/{mediaId}', name: 'del_media')]
    public function delMedia(Post $post = null, int $mediaId): Response
    {
        $this->postService->deleteMedia($post, $mediaId);
        return $this->redirectToRoute(
            'edit_post',
            [
                'id' => $post->getId(),
            ]
        );
    }

    //hero image
    #[Route('/post/{id}/edit/{mediaId}', name: 'edit_post_img')]
    public function setFirstImage(Post $post = null, int $mediaId): Response
    {
        $this->postService->setAsFirstImage($post, $mediaId);
        return $this->redirectToRoute('show_post', [
            'slug' => $post->getSlug(),
        ]);
    }

    //show
    #[Route('/post/{slug}', name: 'show_post')]
    public function show(Post $post, string $slug, Request $request): Response //EntityManagerInterface $manager
    {
        $userConnected = $this->getUser();
        $postModel = $this->postService->getBySlug($slug);
        $formTrack = $this->createForm(TrackFormType::class, new TrackModel());
        $formTrack->handleRequest($request);

        //save track
        if ($formTrack->isSubmitted() && $formTrack->isValid() && $userConnected) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');//is loged
            $this->trackService->saveTrack($formTrack, $post, $this->getUser());
            $this->addFlash(
                'thanks_track',
                'Votre trackaire est publié ! Merci.'
            );
            return $this->redirectToRoute('show_post', ['slug' => $post->getSlug()]);
        }

        //format content
        $root_img = $this->getParameter('post_medias');

        //tracks_pagination
        $limit = $this->trackService::PAGINATOR_PER_PAGE;
        $offset = max(0, $request->query->getInt('offset', 0));
        $tracks = $this->trackService->getTracksPaginator($post, $offset);
        $nbOfTracks = $this->trackService->getNumberOfTracksByPosts($post);
        $nbOfPages = (int) ceil($nbOfTracks / $limit);
        $arrayPages = $this->trackService->getPaginationArrayButtons($nbOfPages);

        if ($post->getStatus() == 1) {
            $template = 'home/post.html.twig';
        } else {
            $template = 'home/post-unpublished.html.twig';
        }
        return $this->render(
            $template,
            [
                'post' => $postModel,
                'tracks' => $tracks,
                'formTrack' => $formTrack->createView(),
                'root_img' => $root_img,
                'user' => $this->getUser(),
                'previous' => $offset - $limit,
                'next' => $offset + $limit,
                'arrayPages' => $arrayPages,
                'nbOfTracks' => $nbOfTracks,
                'pages' => $nbOfPages,
                'page' => $offset,
            ]
        );
    }

    #[Route('/posts', name: 'app_posts')]
    public function home(Request $request): Response
    {
        $limit = $this->postService::PAGINATOR_PER_PAGE;
        $offset = max(0, $request->query->getInt('offset', 0));
        $posts = $this->postService->getPostsPaginator($offset);
        $nbOfPosts = $this->postService->countPostPublished();
        $nbOfPages = (int) ceil($nbOfPosts / $limit);
        $arrayPages = $this->postService->getPaginationArrayButtons($nbOfPages);
        return $this->render('home/posts.html.twig', [
            'pageTitle' => 'All of Posts',
            'posts' => $posts,
            'user' => $this->getUser(),
            'previous' => $offset - $limit,
            'next' => $offset + $limit,
            'arrayPages' => $arrayPages,
            'nbOfPosts' => $nbOfPosts,
            'pages' => $nbOfPages,
            'page' => $offset,
        ]);
    }

    #[Route('', name: 'app_empty')]
    #[Route('/', name: 'app_empty')]
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        $posts = $this->postService->getLastsPosts();
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
            'posts' => $posts,
            'user' => $this->getUser(),
        ]);
    }

}
