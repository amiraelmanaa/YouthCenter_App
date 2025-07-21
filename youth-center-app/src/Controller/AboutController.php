<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;

final class AboutController extends AbstractController
{
    #[Route('/about', name: 'app_about')]
    public function index(\Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator): Response
    {   
        $centers = new \stdClass();
        $missionImage = null; 
        $centers->url = $urlGenerator->generate('app_centerspage');
        return $this->render('about/index.html.twig', [
            'controller_name' => 'AboutController',
            'app_centerspage' => $centers,
            'mission_image' => $missionImage,
        ]);
    }
}
