<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Service\FixturesService;
use App\Entity\Comment;
use App\Entity\Media;
use App\Entity\MediaType;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Cat;
use App\Entity\Tag;
use App\Entity\PostTags;

class AppFixtures extends Fixture
{

    private array $objects = [];

    public function __construct(
        private readonly FixturesService $fixturesService,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly SluggerInterface $slugger,
    ) {
    }

    public function randomObject(string $object)
    {
        $numberOfObject = count($this->objects[$object]) - 1;
        $randomKey = mt_rand(0, $numberOfObject);
        return $this->objects[$object][$randomKey];
    }

    public function images(): void
    {
        $dir = getcwd() . '/public/uploads/';
        $images = scandir($dir);
        unset($images[0]);
        unset($images[1]);
        sort($images);
        $this->objects['images'] = $images;
    }

    public function videos(): void
    {
        $this->objects['video'] = [
            'https://www.youtube.com/watch?v=QLI1uY2id20'
        ];
    }

    public function avatars(): void
    {
        $dir = getcwd() . '/public/avatars/';
        $avatars = scandir($dir);
        unset($avatars[0]);
        unset($avatars[1]);
        sort($avatars);
        $this->objects['avatar'] = $avatars;
    }

    public function users(ObjectManager $manager): void
    {
        for ($i = 0; $i < $this->fixturesService->numberOfUsers(); $i++) {
            $user = new User();
            $password = $this->hasher->hashPassword($user, $this->fixturesService->generalPassword());
            $user
                ->setUser($i == 0 ? $this->fixturesService->adminName() : $this->fixturesService->faker->username)
                ->setEmail($i == 0 ? $this->fixturesService->adminMAil() : $this->fixturesService->faker->email)
                ->setPassword($password)
                ->setRoles([$i == 0 ? 'ROLE_ADMIN' : 'ROLE_EDIT'])
                ->setAvatar($this->randomObject('avatar'));
            $this->objects['user'][] = $user;
            $manager->persist($user);
        }
        $manager->flush();
    }

    public function posts(ObjectManager $manager): void
    {
        for ($i = 0; $i < $this->fixturesService->numberOfPosts(); $i++) {
            $post = new Post();
            $title = $this->fixturesService->faker->sentence($nbWords = 4, $variableNbWords = true);
            $slug = $this->slugger->slug($title);
            $post
                ->setUser($this->randomObject('user'))
                ->setTitle($title)
                ->setSlug($slug->__toString())
                ->setContent($this->fixturesService->faker->paragraphs(mt_rand(4, 7), true))
                ->setImage($this->randomObject('images'))
                ->setCreatedAt($this->fixturesService->generateDateInPast())
                ->setUpdatedAt($this->fixturesService->generateRandomDateFrom())
                ->setStatus(1);
            $this->objects['post'][] = $post;
            $manager->persist($post);
        }
        $manager->flush();
    }

    public function mediaTypes(ObjectManager $manager): void
    {
        foreach (['system', 'avatar', 'image', 'mp4', 'youtube', 'iframe'] as $type) {
            $mediaType = new MediaType();
            $mediaType->setType($type);
            $manager->persist($mediaType);
            $this->objects['mediaType'][] = $mediaType;
        }
        $manager->flush();
    }

    public function medias(ObjectManager $manager): void
    {
        for ($i = 0; $i < $this->fixturesService->numberOfPosts(); $i++) {
            $media = new Media();
            $media
                ->setPost($this->objects['post'][$i])
                ->setFilename($this->randomObject('images'))
                ->setType($this->objects['mediaType'][0]);
            $manager->persist($media);
            $media = new Media();
            $media
                ->setPost($this->objects['post'][$i])
                ->setFilename($this->randomObject('video'))
                ->setType($this->objects['mediaType'][3]);
            $this->objects['youtube'][$i] = $media;
            $manager->persist($media);
        }
        $manager->flush();
    }

    public function comments(ObjectManager $manager): void
    {
        for ($i = 0; $i < $this->fixturesService->numberOfPosts(); $i++) {
            for ($j = 0; $j < 14; $j++) {
                $comments = new Comment();
                $comments
                    ->setPost($this->objects['post'][$i])
                    ->setUser($this->randomObject('user'))
                    ->setContent($this->fixturesService->faker->paragraphs(mt_rand(1, 3), true))
                    ->setStatus(1)
                    ->setDate($this->fixturesService->generateRandomDateFrom());
                $manager->persist($comments);
            }
        }
        $manager->flush();
    }

    public function tags(ObjectManager $manager): void
    {
        foreach ($this->fixturesService->tags() as $key => $categories) {
            foreach ($categories as $category => $tags) {
                $cat = new Cat();
                $cat->setName($category);
                $manager->persist($cat);
                foreach ($tags as $tagname) {
                    $tag = new Tag();
                    $tag->setName($tagname);
                    $tag->setCat($cat);
                    $this->objects['tag'][] = $tag;
                    $manager->persist($tag);
                }
            }
        }
        $manager->flush();
    }

    public function postTags(ObjectManager $manager): void
    {
        for ($i = 0; $i < $this->fixturesService->numberOfPosts(); $i++) {
            for ($j = 0; $j < 4; $j++) {
                $postTags = new PostTags();
                $postTags
                    ->setPost($this->randomObject('post'))
                    ->setTag($this->randomObject('tag'));
                $manager->persist($postTags);
            }
        }
        $manager->flush();
    }

    public function load(ObjectManager $manager): void
    {
        $this->images();
        $this->videos();
        $this->avatars();
        $this->users($manager);
        $this->posts($manager);
        $this->mediaTypes($manager);
        $this->medias($manager);
        $this->comments($manager);
        $this->tags($manager);
        $this->postTags($manager);
    }
}
