<?php

namespace App\Controller\Admin;

use App\Entity\Center;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class CenterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Center::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextEditorField::new('description'),
            TextField::new('name'),
            TextField::new('country'),
            TextField::new('city'),
            TextField::new('address'),
            TextField::new('category'), 
            TextField::new('phone')->setRequired(false),
            TextField::new('email')->setRequired(false),
            AssociationField::new('Manager_ID')->setRequired(false),
            AssociationField::new('activities'),

        ];
    }
   
}
