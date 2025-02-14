<?php

namespace App\Controller\Admin;

use App\Entity\Projet;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class ProjetCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Projet::class;
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
            IdField::new('id')->hideOnForm(), // Caché dans le formulaire de création
            TextField::new('title', 'Titre du projet'),
            TextEditorField::new('content', 'Description'),
            DateTimeField::new('startDate', 'Date de début')->setFormat('dd/MM/yyyy HH:mm'),
            DateTimeField::new('endDate', 'Date de fin prévue')->setFormat('dd/MM/yyyy HH:mm'),
            DateTimeField::new('realEndDate', 'Date de fin réelle')->setFormat('dd/MM/yyyy HH:mm')->hideOnIndex(), // Visible dans le détail mais pas la liste
            AssociationField::new('user', 'Membres de l\'équipe')->setFormTypeOptions(['by_reference' => false]), // ManyToMany
            AssociationField::new('tasks', 'Tâches associées')->hideOnForm(), // Affiché uniquement en lecture
            BooleanField::new('isManuallyTerminated', 'Terminé manuellement'),
        ];
    }

}
