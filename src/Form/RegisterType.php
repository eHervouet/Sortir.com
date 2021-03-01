<?php

namespace App\Form;

use App\Entity\Participants;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('pseudo', null, [
            'label' => 'Pseudo',
            'attr' =>
                ['placeholder' => 'Votre pseudo']
        ])
        ->add('nom', null, [
            'label' => 'Nom',
            'attr' =>
                ['placeholder' => 'Votre nom']
        ])
        ->add('prenom', null, [
            'label' => 'Prénom',
            'attr' =>
                ['placeholder' => 'Votre prénom']
        ])
        ->add('motDePasse', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passes doivent être identiques dans les 2 champs',
            'required' => true,
            'first_options'  => [
                'label' => 'Mot de passe',
                'attr' =>
                    ['placeholder' => '********']
            ],
            'second_options' => [
                'label' => 'Valider le mot de passe',
                'attr' =>
                    ['placeholder' => '********']
            ],
        ])
        ->add('mail', EmailType::class, [
            'label' => 'Adresse mail',
            'attr' =>
                ['placeholder' => 'exemple@this.com']
        ])
        ->add('telephone', TelType::class, [
            'label' => 'Numéro de téléphone',
            'attr' =>
                ['placeholder' => '+33000000000']
        ])
        ->add('add', SubmitType::class, [
            'label' => 'Register'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participants::class,
        ]);
    }
}
