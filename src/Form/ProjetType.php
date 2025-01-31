<?php

namespace App\Form;

use App\Entity\Projet;
use App\Entity\user;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType; // Importer TextType
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Titre',
                ],
            ])
            ->add('content', TextType::class, [
                'label' => 'Contenu',
                'attr' => [
                    'placeholder' => 'Contenu',
                ],
            ])
            ->add('startDate', null, [
                'widget' => 'single_text',
                'label' => 'Date de début',
            ])
            ->add('endDate', null, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
            ])
            ->add('user', EntityType::class, [
                'class' => user::class,
                // Rajouter ici les roles une fois qu'ils seront fait
                'choice_label' => function (User $user) {
                    return $user->getFirstName() . ' ' . $user->getLastName() . ' (' . $user->getSpeciality() . ')';
                },
                'placeholder' => 'Choisir un utilisateur',
                'multiple' => true,
                'expanded' => true, // Affiche les checkbox
                'label' => 'Utilisateurs',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projet::class,
        ]);
    }
}
