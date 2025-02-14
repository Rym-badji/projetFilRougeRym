<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */

    public function configureFields(string $pageName): iterable
    {
        // return [
        //     IdField::new('id')->hideOnForm(), // ID caché dans le formulaire
        //     EmailField::new('email', 'Email'),
        //     ChoiceField::new('roles', 'Rôles')
        //         ->setLabel('Permissions')
        //         ->setHelp('Choix des rôles des membres')
        //         ->setChoices([
        //             'ROLE_ADMIN' => 'ROLE_ADMIN',
        //             'ROLE_CHEF' => 'ROLE_CHEF',
        //             'ROLE_USER' => 'ROLE_USER',
        //         ])
        //         ->allowMultipleChoices(),
        //     TextField::new('matricule', 'Matricule'),
        //     TextField::new('firstName', 'Prénom'),
        //     TextField::new('lastName', 'Nom'),
        //     DateField::new('birthday', 'Date de naissance')->setFormat('dd/MM/yyyy'),
        //     TextField::new('telephone', 'Téléphone'),
        //     TextField::new('service', 'Service'),
        //     TextField::new('speciality', 'Spécialité'),
        //     ImageField::new('photo', 'Photo de profil')
        //         ->setUploadDir('public/uploads/users')
        //         ->setBasePath('/uploads/users')
        //         ->setRequired(false),
        //     AssociationField::new('adresses', 'Adresses')->hideOnIndex(), // Relation OneToMany
        //     AssociationField::new('projets', 'Projets associés')->hideOnIndex(), // Relation ManyToMany
        //     AssociationField::new('tasks', 'Tâches assignées')->hideOnIndex(), // Relation OneToMany
        //     TextareaField::new('password', 'Mot de passe')
        //         ->setFormType(PasswordType::class)
        //         ->onlyOnForms(), // Affiché uniquement dans les formulaires
        // ];
        return [
            TextField::new('email')->setLabel('Adresse électronique'),
            // SlugField::new('slug')->setTargetFieldName('name'),
            TextField::new('matricule')->setLabel('Matricule'),
            TextField::new('firstName')->setLabel('Prénom'),
            TextField::new('lastName')->setLabel('Nom'),
            DateField::new('birthday')->setLabel('Date de naissance'),
            TextField::new('telephone')->setLabel('Téléphone'),
            TextField::new('service')->setLabel('Service'),
            TextField::new('speciality')->setLabel('Spécialité'),
            // MoneyField::new('price')->setCurrency('EUR'),
            // IntegerField::new('quantity'),
            // AssociationField::new('category'),
            ImageField::new('photo')
                ->setBasePath('assets/images/user/')
                ->setUploadDir('public/assets/images/user/')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            ChoiceField::new('roles')
                ->setLabel('Permissions')
                ->setHelp('Choix des rôles des membres')
                ->setChoices([
                'ROLE_ADMIN' => 'ROLE_ADMIN',
                'ROLE_CFIEF' => 'ROLE_CHIEF',
                'ROLE_USER' => 'ROLE_USER',
            ])->allowMultipleChoices(),
        ];
    }
}
