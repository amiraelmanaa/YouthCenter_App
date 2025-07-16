<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CenterManagerController extends AbstractController
{
    #[Route('/center/manager', name: 'app_center_manager')]
    public function index(): Response
    {
        return $this->render('center_manager/index.html.twig', [
            'controller_name' => 'CenterManagerController',
        ]);
    }
}
