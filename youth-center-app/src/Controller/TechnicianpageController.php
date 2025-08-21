<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Technician;
use App\Entity\Assignment;
use App\Repository\AssignmentRepository;

final class TechnicianpageController extends AbstractController
{
    private Security $security;
    private EntityManagerInterface $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }


     //index 
   #[Route('/technicianpage', name: 'app_technicianpage')]
public function index(): Response
{   
    $user = $this->security->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    $technician = $this->em->getRepository(Technician::class)
        ->findOneBy(['email' => $user->getUserIdentifier()]);

    if (!$technician) {
        throw $this->createNotFoundException('Technician not found.');
    }

    $assignments = $this->em->getRepository(Assignment::class)
        ->findBy(['technician' => $technician], ['id' => 'DESC']);

    return $this->render('technicianpage/index.html.twig', [
        'controller_name' => $technician->getName(),
        'assignments' => $assignments,
        'technician' => $technician,
    ]);
}

    #[Route('/technician/dashboard', name: 'technician_dashboard')]
    public function dashboard(): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Find the Technician entity based on the current user
        $technician = $this->em->getRepository(Technician::class)
            ->findOneBy(['email' => $user->getUserIdentifier()]);

        if (!$technician) {
            throw $this->createNotFoundException('Technician not found.');
        }

        // Get assignments for this technician
        $assignments = $this->em->getRepository(Assignment::class)
            ->findBy(['technician' => $technician], ['id' => 'DESC']);

        return $this->render('technician/dashboard.html.twig', [
            'assignments' => $assignments,
            'technician' => $technician,
            'controller_name' => $technician->getName(),
        ]);
    }

    #[Route('/technician/assignment/{id}/accept', name: 'technician_accept_assignment', methods: ['POST'])]
    public function acceptAssignment(int $id): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        $technician = $this->em->getRepository(Technician::class)
            ->findOneBy(['email' => $user->getUserIdentifier()]);

        if (!$technician) {
            return new JsonResponse(['success' => false, 'message' => 'Technician not found'], 404);
        }

        $assignment = $this->em->getRepository(Assignment::class)->find($id);

        if (!$assignment) {
            return new JsonResponse(['success' => false, 'message' => 'Assignment not found'], 404);
        }

        if ($assignment->getTechnician()->getId() !== $technician->getId()) {
            return new JsonResponse(['success' => false, 'message' => 'Not authorized'], 403);
        }

        if ($assignment->getStatus() !== 'pending') {
            return new JsonResponse(['success' => false, 'message' => 'Assignment already processed'], 400);
        }

        try {
            $assignment->setStatus('accepted');
            $this->em->flush();

            return new JsonResponse([
                'success' => true, 
                'message' => 'Assignment accepted successfully',
                'status' => 'accepted'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Failed to accept assignment'], 500);
        }
    }

    #[Route('/technician/assignment/{id}/decline', name: 'technician_decline_assignment', methods: ['POST'])]
    public function declineAssignment(int $id): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        $technician = $this->em->getRepository(Technician::class)
            ->findOneBy(['email' => $user->getUserIdentifier()]);

        if (!$technician) {
            return new JsonResponse(['success' => false, 'message' => 'Technician not found'], 404);
        }

        $assignment = $this->em->getRepository(Assignment::class)->find($id);

        if (!$assignment) {
            return new JsonResponse(['success' => false, 'message' => 'Assignment not found'], 404);
        }

        if ($assignment->getTechnician()->getId() !== $technician->getId()) {
            return new JsonResponse(['success' => false, 'message' => 'Not authorized'], 403);
        }

        if ($assignment->getStatus() !== 'pending') {
            return new JsonResponse(['success' => false, 'message' => 'Assignment already processed'], 400);
        }

        try {
            $assignment->setStatus('declined');
            $this->em->flush();

            return new JsonResponse([
                'success' => true, 
                'message' => 'Assignment declined',
                'status' => 'declined'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Failed to decline assignment'], 500);
        }
    }

    #[Route('/technician/assignment/{id}/complete', name: 'technician_complete_assignment', methods: ['POST'])]
    public function completeAssignment(int $id): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        $technician = $this->em->getRepository(Technician::class)
            ->findOneBy(['email' => $user->getUserIdentifier()]);

        if (!$technician) {
            return new JsonResponse(['success' => false, 'message' => 'Technician not found'], 404);
        }

        $assignment = $this->em->getRepository(Assignment::class)->find($id);

        if (!$assignment) {
            return new JsonResponse(['success' => false, 'message' => 'Assignment not found'], 404);
        }

        if ($assignment->getTechnician()->getId() !== $technician->getId()) {
            return new JsonResponse(['success' => false, 'message' => 'Not authorized'], 403);
        }

        if ($assignment->getStatus() !== 'accepted') {
            return new JsonResponse(['success' => false, 'message' => 'Assignment must be accepted first'], 400);
        }

        try {
            $assignment->setStatus('completed');
            $this->em->flush();

            return new JsonResponse([
                'success' => true, 
                'message' => 'Assignment marked as completed',
                'status' => 'completed'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Failed to complete assignment'], 500);
        }
    }



    // Update Technician Profile
    #[Route('/technician/update-profile', name: 'technician_update_profile', methods: ['POST'])]
public function updateProfile(Request $request): JsonResponse
{
    $user = $this->security->getUser();
    if (!$user) {
        return new JsonResponse(['success' => false, 'message' => 'Not authenticated'], 401);
    }

    $technician = $this->em->getRepository(Technician::class)
        ->findOneBy(['email' => $user->getUserIdentifier()]);

    if (!$technician) {
        return new JsonResponse(['success' => false, 'message' => 'Technician not found'], 404);
    }

    $data = json_decode($request->getContent(), true);

    if (!isset($data['name'], $data['email'])) {
        return new JsonResponse(['success' => false, 'message' => 'Invalid data'], 400);
    }

    try {
        $technician->setName($data['name']);
        $technician->setEmail($data['email']);
        $technician->setspecialization ($data['specialization '] ?? null);
        $technician->setRegion($data['region'] ?? null);
        $technician->setPhone($data['phone'] ?? null);

        $this->em->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Profile updated successfully',
        ]);
    } catch (\Exception $e) {
        return new JsonResponse([
            'success' => false,
            'message' => 'Failed to update profile',
        ], 500);
    }
}

}