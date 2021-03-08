<?php

namespace App\Form;

use App\Entity\Lieux;
use App\Entity\Sorties;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;

class CreateSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie :'
            ])
            ->add('datedebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie :'
            ])
            ->add('datecloture', DateTimeType::class, [
                'label' => 'Date limite d\'inscription :'
            ])
            ->add('nbinscriptionsmax', NumberType::class, [
                'label' => 'Nombre de place :',
                'html5' => true
            ])
            ->add('duree', NumberType::class, [
                'label' => 'Durée :',
                'html5' => true
            ])
            ->add('descriptioninfos', TextareaType::class, [
                'label' => 'Description et infos :'
            ])
            ->add('lieuxnolieu', EntityType::class, [
                'label' => 'Lieu :',
                'class' => Lieux::class,
                'choice_label' => 'nomLieu'
            ])
            ->add('enregistrer', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->add('annuler', ResetType::class, [
                'label' => 'Annuler'
            ]);
        ;
    }
}