<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CenterRepository;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(CenterRepository $centerRepository): Response
    {
         $centers = $centerRepository->findAll();

    return $this->render('home/index.html.twig', [
        'controller_name' => 'HomeController',
        'centers' => $centers,
    ]);
}
}