<?php

namespace App\Controller;

use App\Entity\Center;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class CenterspageController extends AbstractController
{
    #[Route('/centerspage', name: 'app_centerspage')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $country = $request->query->get('country');
        $city = $request->query->get('city');
        $activity = $request->query->get('activity');

        $repo = $em->getRepository(Center::class);
        $qb = $repo->createQueryBuilder('c');

        if ($country) {
            $qb->andWhere('c.country = :country')->setParameter('country', $country);
        }

        if ($city) {
            $qb->andWhere('c.city = :city')->setParameter('city', $city);
        }

        if ($activity) {
    $qb->join('c.activities', 'a')
       ->andWhere('a.name = :activity')
       ->setParameter('activity', $activity);
}

        $centers = $qb->getQuery()->getResult();

        return $this->render('centerspage/index.html.twig', [
            'centers' => $centers,
            'filters' => [
                'country' => $country,
                'city' => $city,
                'activity' => $activity
            ]
        ]);
    }
}
