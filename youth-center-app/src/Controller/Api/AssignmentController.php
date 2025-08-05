<?php


declare(strict_types=1);
namespace App\Controller\Api;
use App\Entity\Assignment;
use App\Entity\Technician;
use App\Entity\CenterManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;


class AssignmentController extends AbstractController
{
    /**
     * @Route("/api/assignments", name="api_create_assignment", methods={"POST"})
     */
   public function createAssignment(Request $request, EntityManagerInterface $em, PublisherInterface $publisher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $assignment = new Assignment();
        $assignment->setDescription($data['description']);
        $assignment->setPriority($data['priority']);
        $assignment->setStatus('pending');
        
        $technician = $em->getRepository(Technician::class)->find($data['id']);
        $manager = $this->getUser();
        
        $assignment->setTechnician($technician);
        $assignment->setManager($manager);
        
        $em->persist($assignment);
        $em->flush();
        
         $update = new Update(
        "/assignments/technician/{$technician->getId()}",
        json_encode([
            'type' => 'assignment',
            'id' => $assignment->getId(),
            'description' => $assignment->getDescription(),
            'priority' => $assignment->getPriority(),
            'assignedBy' => $assignment->getManager()
        ])
    );
    
    $publisher($update);
    
    return $this->json(['success' => true]);
    }

    /**
     * @Route("/api/assignments/{id}/status", name="api_update_assignment_status", methods={"PUT"})
     */
    public function updateAssignmentStatus(Assignment $assignment, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $assignment->setStatus($data['status']);
        $em->flush();
        
        return $this->json(['success' => true]);
    }
}