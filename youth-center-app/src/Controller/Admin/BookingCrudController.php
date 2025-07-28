<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\Date;
use EasyCorp\Bundle\EasyAdminBundle\Field\DatetimeField;

class BookingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Booking::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DatetimeField::new('startDate')
                ->setFormat('dd-MM-yyyy HH:mm')
                ->setRequired(true)
                ->setHelp('Select the date and time for the booking.'),
            DatetimeField::new('endDate')
                ->setFormat('dd-MM-yyyy HH:mm')
                ->setRequired(true)
                ->setHelp('Select the end date and time for the booking.'),
            TextField::new('status')
                ->setRequired(true)
                ->setHelp('Enter the status of the booking (e.g., confirmed, pending, cancelled).'),
            

            
        ];
    
 
}
        */
}