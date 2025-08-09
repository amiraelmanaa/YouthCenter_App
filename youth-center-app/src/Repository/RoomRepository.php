<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Room>
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }
    /*

 public function isAvailable(Room $room, \DateTimeInterface $start, \DateTimeInterface $end): bool
{
    $qb = $this->createQueryBuilder('r')
        ->select('count(b.id)')
        ->join('r.bookings', 'b')
        ->where('r = :room')
        ->andWhere('b.startDate < :end')
        ->andWhere('b.endDate > :start')
        ->setParameter('room', $room)
        ->setParameter('start', $start)
        ->setParameter('end', $end);
        
    $count = (int) $qb->getQuery()->getSingleScalarResult();

    return $count === 0;
}
*/
public function findAvailableRooms(\DateTime $start, \DateTime $end, int $guests, bool $isGroup, int $centerId)
{
    $qb = $this->createQueryBuilder('r')
        ->join('r.center', 'c') 
        ->andWhere('r.capacity >= :guests')
        ->andWhere('(r.is_group_only = :isGroup OR :isGroup = false)')
        ->andWhere('c.id = :centerId')
        ->andWhere('r.id NOT IN (
            SELECT r2.id FROM App\Entity\Booking b
            JOIN b.room r2
            WHERE b.startDate < :end AND b.endDate > :start
        )')
        ->setParameter('guests', $guests)
        ->setParameter('isGroup', $isGroup)
        ->setParameter('centerId', $centerId)
        ->setParameter('start', $start)
        ->setParameter('end', $end);

    return $qb->getQuery()->getResult();
}




}
    

    //    /**
    //     * @return Room[] Returns an array of Room objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Room
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
