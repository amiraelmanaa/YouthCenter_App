<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Booking;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Repository\BookingRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[IsGranted('ROLE_USER')]
final class UserBookingsJournalController extends AbstractController
{
    #[Route('/user/bookings/journal', name: 'app_user_bookings_journal')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {   
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedException('You must be logged in to view your bookings.');
        }
        
       
        $status = $request->query->get('status', 'all');
        $sortBy = $request->query->get('sort', 'date_desc');
        
       
        $queryBuilder = $entityManager->getRepository(Booking::class)->createQueryBuilder('b');
        
   
        if ($user && method_exists($user, 'getEmail')) {
            $queryBuilder->where('b.email = :email')
                        ->setParameter('email', $user->getEmail());
        } else {
           
            $queryBuilder->where('1 = 0'); 
        }
        
        if ($status !== 'all') {
            $queryBuilder->andWhere('b.status = :status')
                        ->setParameter('status', $status);
        }
        
    
        switch ($sortBy) {
            case 'date_asc':
                $queryBuilder->orderBy('b.startDate', 'ASC');
                break;
            case 'date_desc':
            default:
                $queryBuilder->orderBy('b.startDate', 'DESC');
                break;
            case 'status':
                $queryBuilder->orderBy('b.status', 'ASC');
                break;
            case 'price_asc':
                $queryBuilder->orderBy('b.totalPrice', 'ASC');
                break;
            case 'price_desc':
                $queryBuilder->orderBy('b.totalPrice', 'DESC');
                break;
        }
        
        $bookings = $queryBuilder->getQuery()->getResult();
        
        //  statistics
        $stats = $this->calculateBookingStats($bookings);
        
     
        $availableStatuses = [];
        foreach ($bookings as $booking) {
            if ($booking->getStatus()) {
                $availableStatuses[] = $booking->getStatus();
            }
        }
        $availableStatuses = array_unique($availableStatuses);

        return $this->render('user_bookings_journal/index.html.twig', [
            'bookings' => $bookings, 
            'stats' => $stats,
            'current_status_filter' => $status,
            'current_sort' => $sortBy,
            'available_statuses' => $availableStatuses,
        ]);
    }
    
    #[Route('/user/bookings/journal/{id}', name: 'app_user_booking_detail')]
    public function detail(Booking $booking): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedException('You must be logged in to view booking details.');
        }
        

        if ($user && method_exists($user, 'getEmail')) {

            if ($booking->getEmail() !== $user->getEmail()) {
                throw $this->createAccessDeniedException('You can only view your own bookings.');
            }
        }
        
        return $this->render('user_bookings_journal/detail.html.twig', [
            'booking' => $booking,
        ]);
    }
    
    private function calculateBookingStats(array $bookings): array
    {
        $stats = [
            'total' => count($bookings),
            'confirmed' => 0,
            'pending' => 0,
            'cancelled' => 0,
            'completed' => 0,
            'total_spent' => 0,
            'upcoming' => 0,
            'past' => 0,
        ];
        
        $now = new \DateTime();
        
        foreach ($bookings as $booking) {
      
            $status = strtolower($booking->getStatus() ?? '');
            if (isset($stats[$status])) {
                $stats[$status]++;
            }
            
            // total spent
            $stats['total_spent'] += $booking->getTotalPrice() ?? 0;
            
            
            if ($booking->getStartDate() && $booking->getStartDate() > $now) {
                $stats['upcoming']++;
            } else {
                $stats['past']++;
            }
        }
        
        return $stats;
    }
}