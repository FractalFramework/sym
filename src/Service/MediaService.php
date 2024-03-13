<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\MediaTypeRepository;
use App\Repository\MediaRepository;
use App\Repository\PostRepository;
use App\Entity\Post;
use App\Entity\Media;
use App\Entity\MediaType;

class MediaService extends AbstractController
{

    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly MediaRepository $mediaRepository,
        private readonly MediaTypeRepository $mediaTypeRepository,
    ) {

    }

    public function saveMedia(
        Post $post,
        string $mediaFileName,
    ): void {
        $media = new Media();
        if ($pos = strpos($mediaFileName, '&'))
            $mediaFileName = substr($mediaFileName, 0, $pos - 1);
        $media->setFilename($mediaFileName);
        //other types availables but unused: video, avatar, et so on
        $mediaType = strpos($mediaFileName, 'youtube.com') ? 'youtube' : 'image';
        $mediaTypeEntity = $this->getMediaType($mediaType);
        $media->setType($mediaTypeEntity);
        $media->setPost($post);
        $this->mediaRepository->saveMedia($media);
        //could assign first uploaded image to hero image
    }

    public function getMediaType(string $mediaType): MediaType|null
    {
        return $this->mediaTypeRepository->findOneByType($mediaType);
    }

    public function getCatalog(Post $post): Collection|null
    {
        return $this->mediaRepository->findByPost($post);
    }

    public function youtubeEmbedUrl(string $url): string
    {
        //input: https://youtu.be/hW_RhD0D-Ew
        $url = str_replace('youtu.be', 'youtube.com', $url);
        //input: https://www.youtube.com/watch?v=hW_RhD0D-Ew
        $url = str_replace('watch?v=', '', $url);
        //output: https://www.youtube.com/embed/hW_RhD0D-Ew
        $url = str_replace('youtube.com/', 'youtube.com/embed/', $url);
        return $url;
    }

    public function youtubeImage(string $url): string
    {
        return 'https://img.youtube.com/vi/' . substr(strchr($url, '/'), 1);
    }

    public function goodUrl(string $url): string
    {
        if (strpos($url, 'youtube.com')) {
            $url = $this->youtubeEmbedUrl($url);
        } else {
            $url = '/uploads/' . $url;
        }
        return $url;
    }

}
