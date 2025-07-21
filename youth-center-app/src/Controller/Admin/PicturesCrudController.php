<?php

namespace App\Controller\Admin;

use App\Entity\Pictures;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class PicturesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Pictures::class;
    }

    
   
    public function configureFields(string $pageName): iterable
{
    return [
        TextField::new('imageName', 'Image Name')
            ->setRequired(true)
            ->setHelp('Enter the name of the image file.'),
        AssociationField::new('Center_ID', 'Center')
            ->setRequired(true),
         ImageField::new('imageName', 'Picture')
            ->setBasePath('') 
            ->setUploadDir('public/') 
            ->setRequired(true),
    ];
}
}
