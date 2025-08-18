<?php
// src/Repository/PayPalRepository.php

namespace App\Repository;

use App\Entity\PayPal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PayPalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PayPal::class);
    }

    /**
     * Find PayPal payments by booking
     */
    public function findByBooking($booking): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.BookingID = :booking')
            ->setParameter('booking', $booking)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}