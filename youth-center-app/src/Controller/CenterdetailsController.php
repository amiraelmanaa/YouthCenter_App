<?php
namespace App\Controller;

use App\Entity\Center;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class CenterdetailsController extends AbstractController
{
    #[Route('/centerdetails', name: 'app_centerdetails_default')]
    public function indexDefault(EntityManagerInterface $em): Response
    {
        $repo = $em->getRepository(Center::class);
        $center = $repo->findOneBy([]); // Gets first center for testing

        if (!$center) {
            throw $this->createNotFoundException('No youth centers found.');
        }

        return $this->render('centerdetails/index.html.twig', [
            'center' => $center,
        ]);
    }

    #[Route('/centerdetails/{id}', name: 'app_centerdetails', requirements: ['id' => '\d+'])]
    public function index(int $id, EntityManagerInterface $em): Response
    {
        $repo = $em->getRepository(Center::class);
        $center = $repo->find($id);

        if (!$center) {
            throw $this->createNotFoundException('Youth center not found.');
        }

        return $this->render('centerdetails/index.html.twig', [
            'center' => $center,
        ]);
    }
}