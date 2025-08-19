<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LogintermsController extends AbstractController
{
    #[Route('/loginterms', name: 'app_loginterms')]
    public function index(): Response
    {
        return $this->render('loginterms/index.html.twig', [
            'controller_name' => 'LogintermsController',
        ]);
    }
}
