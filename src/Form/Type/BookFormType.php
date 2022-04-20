<?php

// src/Form/Type/TaskType.php
namespace App\Form\Type;

use App\Form\Model\BookDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class BookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title', TextType::class)
        ->add('categories', CollectionType::class, [
            'allow_add' => true,
            'allow_delete' => true,
            'entry_type' => CategoryFormType::class
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BookDto::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

    public function getName(){
        return '';
    }
}