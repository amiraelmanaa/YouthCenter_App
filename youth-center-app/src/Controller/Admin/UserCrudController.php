<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;


class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('email')->setLabel('Email Address'),
            ChoiceField::new('roles')
    ->setChoices([
        'Admin' => 'ROLE_ADMIN',
        'User' => 'ROLE_USER',
        'Center Manager' => 'ROLE_CENTERMANAGER',
        'Technician' => 'ROLE_TECHNICIAN',
    ])
    ->allowMultipleChoices()

        ];
    }
    
}
