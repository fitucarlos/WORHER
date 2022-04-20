<?php

// src/Form/Type/TaskType.php
namespace App\Form\Type;

use App\Entity\Tarea;
use App\Form\Model\TareaDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class TareaFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('id', TextType::class)
        ->add('nombre', TextType::class)
        ->add('descripcion', TextType::class)
        ->add('dificultad', TextType::class)
        ->add('prioridad', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TareaDto::class,
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