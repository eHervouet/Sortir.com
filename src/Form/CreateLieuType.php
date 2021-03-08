<?php

namespace App\Form;

use App\Entity\Lieux;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;

class CreateLieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomLieu', TextType::class, [
                'label' => 'Nom du lieu :'
            ])
            ->add('rue', TextType::class, [
                'label' => 'Rue :',
                'attr' => [
                    'class' => 'addresse',
                    'readonly' => true
                ]
            ])
            ->add('villesNoVille', TextType::class, [
                'label' => 'Ville :',
                'attr' => [
                    'class' => 'ville',
                    'readonly' => true
                ]
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude :',
                'attr' => [
                    'class' => 'lat',
                    'readonly' => true
                ]
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude :',
                'attr' => [
                    'class' => 'lon',
                    'readonly' => true
                ]
            ])
            ->add('cp', HiddenType::class, [
                'attr' => [
                    'class' => 'cp'
                ]
            ])
            ->add('enregistrer', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->add('annuler', ResetType::class, [
                'label' => 'Annuler'
            ]);
    }

    //
}