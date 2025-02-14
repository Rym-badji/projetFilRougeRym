<?php

namespace App\Controller\Admin;

use App\Entity\Task;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class TaskCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Task::class;
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
        return [
            IdField::new('id')->hideOnForm(), // ID caché dans le formulaire
            TextField::new('title', 'Titre de la tâche'),
            TextEditorField::new('content', 'Description'),
            DateTimeField::new('startDate', 'Date de début')->setFormat('dd/MM/yyyy HH:mm'),
            DateTimeField::new('endDate', 'Date de fin prévue')->setFormat('dd/MM/yyyy HH:mm'),
            DateTimeField::new('realEndDate', 'Date de fin réelle')->setFormat('dd/MM/yyyy HH:mm')->hideOnIndex(), // Caché dans la liste
            AssociationField::new('projet', 'Projet associé')->setRequired(true), // Relation ManyToOne
            AssociationField::new('user', 'Utilisateur assigné')->setRequired(false), // Optionnel
            BooleanField::new('isManuallyTerminated', 'Terminée manuellement'),
        ];
    }
}
