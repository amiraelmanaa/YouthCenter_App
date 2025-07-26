<?php

namespace App\Controller\Admin;

use App\Entity\Room;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Config\Definition\NumericNode;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class RoomCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Room::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            NumberField::new('capacity', 'Room Capacity'),
            NumberField::new('price_per_night', 'Price per Night'),
            BooleanField::new('is_group_only', 'Group Only'),
           AssociationField::new('center', 'Center')
                ->autocomplete() 
        ];
    }
    
}
