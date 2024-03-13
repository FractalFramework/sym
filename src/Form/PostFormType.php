<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\UserRepository;
use App\Service\MediaService;
use App\Entity\Post;

class PostFormType extends AbstractType
{

    public function __construct(
        //private AbstractController $abstractController
        private readonly UserRepository $userRepository,
        private readonly SluggerInterface $slugger,
        private readonly MediaService $mediaService,
    ) {

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'attr' => [
                        'class' => 'form-control mb-3'
                    ],
                    'label' => 'Titre',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Entrez un titre',
                        ]),
                        new Length([
                            'min' => 4,
                            'minMessage' => 'mini {{ limit }} caractères',
                            'max' => 255,
                            'maxMessage' => 'max {{ limit }} caractères',
                        ]),
                    ]
                ]
            )
            ->add(
                'content',
                TextareaType::class,
                [
                    'attr' => [
                        'class' => 'form-control mb-3',
                        'rows' => '12'
                    ],
                    'label' => 'Description',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Le contenu ne peut être vide',
                        ]),
                        new Length([
                            'min' => 100,
                            'minMessage' => 'mini {{ limit }} caractères',
                        ]),
                    ],
                ]
            )
            ->add(
                'image',
                HiddenType::class,
                [
                    'attr' => [
                        'value' => 'http://placehold.it/600x200'
                    ],
                ]
            )
            ->add(
                'video',
                UrlType::class,
                [
                    'attr' => [
                        'class' => 'form-control mb-3'
                    ],
                    'label' => 'Url Youtube seulement',
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new Regex(
                            [
                                'pattern' => '/https?:\/\/www\.youtube\.com/',
                                'message' => 'Seuls les liens "youtube.com" sont actuellement pris en charge'
                            ]
                        )
                    ]
                ]
            )
            ->add(
                'media',
                FileType::class,
                [
                    'label' => 'Image',
                    'attr' => [
                        'class' => 'form-control mb-3'
                    ],

                    //iterable
                    'multiple' => true,

                    // unmapped means that this field is not associated to any entity property
                    'mapped' => false,

                    // make it optional so you don't have to re-upload the PDF file
                    // every time you edit the Product details
                    'required' => false,

                    // unmapped fields can't define their validation using attributes
                    // in the associated entity, so you can use the PHP constraint classes
                    'constraints' => [
                        new All(
                            new Image(
                                [
                                    'maxWidth' => 8192,
                                    'maxWidthMessage' => 'Largeur max : {{ max_width }}',
                                    'maxHeight' => 8192,
                                    'maxHeightMessage' => 'Hauteur max : {{ max_height }}',
                                ]
                            )
                        ),
                    ],
                ]
            )

            ->add('Enregistrer', SubmitType::class)
            ->getForm();
    }

}
