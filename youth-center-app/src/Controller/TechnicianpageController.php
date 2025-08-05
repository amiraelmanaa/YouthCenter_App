<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MaintenanceAssignmentRepository;
use App\Entity\Technician;
use App\Repository\TechnicianRepository;

final class TechnicianpageController extends AbstractController
{
    #[Route('/technicianpage', name: 'app_technicianpage')]
    public function index(): Response
    {
        return $this->render('technicianpage/index.html.twig', [
            'controller_name' => 'TechnicianpageController',
        ]);
    }
    #[Route('/technician/dashboard', name: 'technician_dashboard')]
public function dashboard(TechnicianRepository $assignmentRepo): Response
{
    $technician = $this->getUser();
    if (!$technician instanceof Technician) {
        throw $this->createAccessDeniedException('You must be logged in as a technician to access this page.');
    }

    $assignments = $assignmentRepo->findBy(['technician' => $technician]);

    return $this->render('technician/dashboard.html.twig', [
        'assignments' => $assignments,
        'controller_name' => $technician->getName(), 
    ]);
}

}
