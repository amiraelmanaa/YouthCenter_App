<?php
namespace App\EventListener;
use App\Entity\User;
use App\Entity\Technician;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
#[AsEntityListener(event: 'postPersist', entity: User::class)]
class UserEntityListener
{
    public function postPersist(User $user, LifecycleEventArgs $args): void
    {
        if (in_array('ROLE_TECHNICIAN', $user->getRoles(), true)) {
            $technician = new Technician();
            $technician->setUser($user);
            $technician->setEmail($user->getEmail());

            $em = $args->getObjectManager();
            $em->persist($technician);
            $em->flush();
        }
    }
}
