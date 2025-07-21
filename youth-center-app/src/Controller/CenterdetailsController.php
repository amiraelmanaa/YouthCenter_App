<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CenterdetailsController extends AbstractController
{
    #[Route('/centerdetails', name: 'app_centerdetails')]
    public function index(): Response
    {
        return $this->render('centerdetails/index.html.twig', [
            'controller_name' => 'CenterdetailsController',
        ]);
    }
}
