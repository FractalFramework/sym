<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use App\Service\UserService;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AvatarFormType extends AbstractType
{

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly SluggerInterface $slugger,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'avatar',
                ChoiceType::class,
                [
                    'attr' => [
                        'class' => 'form-control mb-3'
                    ],
                    'choice_label' => fn(UserService $userService) => $userService->chooseAvatar(),
                    'expanded' => true,
                    'multiple' => false,
                    'label' => 'Avatar',
                ]
            )
            ->add('save', SubmitType::class, [
                'label' => 'Choisir',
                'disabled' => false,
            ])
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}
