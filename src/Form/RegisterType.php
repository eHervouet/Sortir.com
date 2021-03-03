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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('pseudo', TextType::class, [
            'label' => 'Pseudo :    ',
            'attr' =>
                ['placeholder' => '']
        ])
        ->add('prenom', TextType::class, [
            'label' => 'Prénom :',
            'attr' =>
                ['placeholder' => '']
        ])
        ->add('nom', TextType::class, [
            'label' => 'Nom :',
            'attr' =>
                ['placeholder' => '']
        ])
        ->add('telephone', TelType::class, [
            'label' => 'Téléphone :',
            'attr' =>
                ['placeholder' => '+33']
        ])
        ->add('mail', EmailType::class, [
            'label' => 'Email :',
            'attr' =>
                ['placeholder' => 'exemple@domaine.ex']
        ])
        ->add('motDePasse', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passes doivent être identiques dans les 2 champs',
            'required' => true,
            'first_options'  => [
                'label' => 'Mot de passe :',
                'attr' =>
                    ['placeholder' => '********']
            ],
            'second_options' => [
                'label' => 'Confirmation :',
                'attr' =>
                    ['placeholder' => '********']
            ],
        ])
        ->add('sitesNoSite', null, [
            'label' => 'Site de rattachement :',
            'choice_label' => 'nomSite',
            'placeholder' => false,
        ])
        ->add('profilPicture', FileType::class, [
            'label' => 'Ma photo :',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'img/png',
                            'img/jpg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid profil picture (jpg/png)',
                    ])
                ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participants::class,
        ]);
    }
}
