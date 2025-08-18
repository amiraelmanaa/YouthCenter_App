<?php
// src/Repository/VirementRepository.php

namespace App\Repository;

use App\Entity\Virement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VirementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Virement::class);
    }

    /**
     * Find bank transfer payments by booking
     */
    public function findByBooking($booking): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.BookingId = :booking')
            ->setParameter('booking', $booking)
            ->orderBy('v.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
