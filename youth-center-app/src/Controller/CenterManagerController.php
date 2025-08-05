<?php

namespace App\Controller;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security; 
use App\Entity\Center;
use App\Entity\CenterManager;
use App\Entity\Booking;
use App\Entity\Technician;
use App\Entity\User;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Assignment;
use App\Repository\TechnicianRepository;
use Symfony\Component\Security\Core\Security as CoreSecurity;

final class CenterManagerController extends AbstractController
{
    private Security $security;
    private EntityManagerInterface $em;
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(Security $security, EntityManagerInterface $em, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->security = $security;
        $this->em = $em;
        $this->csrfTokenManager = $csrfTokenManager;
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
//technician assignment
    #[Route('/manager/assign-technician', name: 'manager_assign_technician', methods: ['POST'])]
    public function assignTechnician(
        Request $request,
        EntityManagerInterface $em,
        TechnicianRepository $technicianRepo,
        Security $security
    ): Response {
        
        $user = $security->getUser();
        if (!$user) {
            $this->addFlash('error', 'You must be logged in.');
            return $this->redirectToRoute('app_login');
        }

    
        $manager = $em->getRepository(CenterManager::class)
            ->findOneBy(['email' => $user->getUserIdentifier()]);

        if (!$manager) {
            $this->addFlash('error', 'Center manager not found.');
            return $this->redirectToRoute('app_center_manager');
        }

        $technicianId = $request->request->get('technician_id');
        $description = $request->request->get('description');
        $priority = $request->request->get('priority');

        // CSRF Token validation
        $token = $request->request->get('token');
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('assign-technician', $token))) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_center_manager');
        }

        // Validate required fields
        if (!$technicianId || !$description || !$priority) {
            $this->addFlash('error', 'All fields are required.');
            return $this->redirectToRoute('app_center_manager');
        }

        // Find technician
        $technician = $technicianRepo->find($technicianId);
        if (!$technician) {
            $this->addFlash('error', 'Technician not found.');
            return $this->redirectToRoute('app_center_manager');
        }

        try {
            // Create new assignment
            $assignment = new Assignment();
            $assignment->setTechnician($technician);
            $assignment->setManager($manager);
            $assignment->setDescription($description);
            $assignment->setPriority($priority);
            $assignment->setStatus('pending'); 

            $em->persist($assignment);
            $em->flush();

            $this->addFlash('success', 'Technician assigned successfully.');
        } catch (\Exception $e) {
            //  debugging
            error_log('Assignment creation failed: ' . $e->getMessage());
            $this->addFlash('error', 'Failed to assign technician. Please try again.');
        }

        return $this->redirectToRoute('app_center_manager');
    }
}