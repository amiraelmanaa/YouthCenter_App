<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Booking;
use App\Repository\BookingRepository;

final class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'app_payment')]
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }


    #[Route('/payment/booking/{id}', name: 'app_payment_booking')]
    public function booking(Booking $booking, BookingRepository $bookingRepository): Response
    {
        // Check if the booking exists  
        if (!$booking) {
            throw $this->createNotFoundException('Booking not found');
        }
        // Check if the booking is already paid
        if ($booking->isPaid()) {
            return $this->redirectToRoute('app_booking_show', ['id' => $booking->getId()]);
        }
        // Check if the booking belongs to the current user
        $user = $this->getUser();
        
        // Render the payment page with the booking details
        return $this->render('payment/index.html.twig', [
            'booking' => $booking,
            'user' => $user,
        ]);

    }
}
