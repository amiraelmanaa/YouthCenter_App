<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security; // Updated import
use App\Entity\Center;
use App\Entity\CenterManager;
use App\Entity\Booking;
use App\Entity\Technician;
use App\Entity\User;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;


final class CenterManagerController extends AbstractController
{
    private Security $security;
    private EntityManagerInterface $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }

   #[Route('/center/manager', name: 'app_center_manager')]
public function index(): Response
{
    $user = $this->security->getUser();
    
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    $centerManager = $this->em->getRepository(CenterManager::class)
        ->findOneBy(['email' => $user->getUserIdentifier()]);

    if (!$centerManager) {
        throw $this->createNotFoundException('Center manager not found.');
    }

    $center = $centerManager->getCenter();

    if (!$center) {
        throw $this->createNotFoundException('No center assigned to this manager.');
    }

    // Fetch all bookings for this center's rooms
    $bookings = $this->em->getRepository(Booking::class)
        ->createQueryBuilder('b')
        ->join('b.room', 'r')
        ->where('r.center = :center')
        ->setParameter('center', $center)
        ->orderBy('b.startDate', 'DESC')
        ->getQuery()
        ->getResult();

 $technicalUsers = $this->em->getRepository(Technician::class)->findAll();

    

    return $this->render('center_manager/index.html.twig', [
        'center' => $center,
        'manager' => $centerManager,
        'bookings' => $bookings,
       
        'technicians' => $technicalUsers,
    ]);
}

#[Route('/manager/booking/{id}/{action}', name: 'manager_booking_action', methods: ['POST'])]
public function handleBooking(
    int $id,
    string $action,
    Request $request,
    EntityManagerInterface $em
): JsonResponse {
    $booking = $em->getRepository(Booking::class)->find($id);

    if (!$booking) {
        return new JsonResponse(['success' => false, 'message' => 'Booking not found.'], 404);
    }

    if (!in_array($action, ['accept', 'decline'])) {
        return new JsonResponse(['success' => false, 'message' => 'Invalid action.'], 400);
    }

    $booking->setStatus($action === 'accept' ? 'accepted' : 'declined');
    $em->flush();

    return new JsonResponse(['success' => true]);
}
}