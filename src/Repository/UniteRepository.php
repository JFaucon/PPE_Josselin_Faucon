<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\Unite;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Unite>
 *
 * @method Unite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Unite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Unite[]    findAll()
 * @method Unite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UniteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Unite::class);
    }

    /**
     * @return Unite[] Returns an array of Unite objects
     */
    public function findByForfait(int $value): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.available = TRUE')
            ->orderBy('u.id', 'ASC')
            ->setMaxResults($value)
            //->setParameter(':val', $value)

            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Unite[] Returns an array of Unite objects
     */
    public function findByReservation(Reservation $reservation): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.reservation = :val')
            ->orderBy('u.id', 'ASC')
            ->setParameter('val', $reservation->getId())

            ->getQuery()
            ->getResult()
            ;
    }

    public function findByUser($value): array
    {
        return $this->createQueryBuilder('un')
            ->select('un.id')
            ->innerJoin('un.reservation', 'r')
            ->andWhere('r.userr = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getSingleColumnResult()
            ;
    }

    public function UpdateByReservation(Reservation $reservation): int
    {
        return $this->createQueryBuilder('u')
            ->update(Unite::class,'u')
            ->set('u.available','TRUE')
            ->set('u.reservation','NULL')
            ->andWhere('u.reservation = :val')
            ->setParameter('val', $reservation->getId())

            ->getQuery()
            ->getSingleScalarResult()
            ;
    }
}
