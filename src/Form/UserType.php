<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('matricule', TextType::class, [
                'label' => 'Matricule',
                'attr' => [
                    'placeholder' => 'votre matricule de référence',
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'votre prénom de référence',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'votre nom de référence',
                ],
            ])
            ->add('birthday', DateType::class, [
                // 'widget' => 'single_text',
                'label' => 'Date de naissance',
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => 'votre numéro de téléphone',
                ],
            ])
            ->add('service', TextType::class, [
                'label' => 'Service',
                'attr' => [
                    'placeholder' => 'votre service d\'affectation',
                ],
            ])
            ->add('speciality', TextType::class, [
                'label' => 'Spécialité',
                'attr' => [
                    'placeholder' => 'votre spécialité',
                ],
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo',
                'attr' => [
                    'placeholder' => 'votre photo Max 2Mo',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse éléctronique',
                'attr' => [
                    'placeholder' => 'votre adresse éléctronique valide',
                ],
                ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mots de passe et la confirmation doivent être identiques.',
                'label' => 'Mot de passe',
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => 'votre mot de passe de génie',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => [
                    'placeholder' => 'confirmer votre mot de passe',
                    ],
                ],
            ])
    
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
