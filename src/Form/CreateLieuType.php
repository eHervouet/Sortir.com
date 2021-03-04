<?php

namespace App\Form;

use App\Entity\Lieux;
use Symfony\Component\Form\AbstractType;
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
                'label' => 'Rue :'
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude :',
                'html5' => true
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude :',
                'html5' => true
            ])
            ->add('villesNoVille', null, [
                'label' => 'Ville :',
                'choice_label' => 'nomVille',
                'placeholder' => false,
            ])
            ->add('enregistrer', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->add('annuler', ResetType::class, [
                'label' => 'Annuler'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieux::class,
        ]);
    }
}