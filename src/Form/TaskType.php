<?php

namespace App\Form;

use App\Entity\Projet;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType; // Importer TextType
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
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
            ->add('projet', EntityType::class, [
                'class' => Projet::class,
                'choice_label' => function (Projet $projet) {
                    return $projet->getTitle();
                },
                'label' => 'Projet',
                'placeholder' => 'Choisir un projet',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getFirstName() . ' ' . $user->getLastName() . ' (' . $user->getSpeciality() . ')';
                },
                'label' => 'Utilisateurs',
                'placeholder' => 'Choisir un utilisateur',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
