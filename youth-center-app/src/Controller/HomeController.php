<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CenterRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;  

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        CenterRepository $centerRepository, 
        UrlGeneratorInterface $urlGenerator
    ): Response {
        $centers = $centerRepository->findAll();

        // Generate URLs for each center
        foreach ($centers as $center) {
            $center->url = $urlGenerator->generate('app_centerdetails', [
                'id' => $center->getId(),
            ]);
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'centers' => $centers,
        ]);
    }
}
