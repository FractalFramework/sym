<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class PostTagsFormType extends AbstractType
{

    public function __construct(
    ) {

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // here we could use an addEventListener(FormEvents::PRE_SET_DATA to avoid to validate the form with nothing
            ->add('tagId')
            ->getForm();
    }

}
