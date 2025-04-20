<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Find the ID of a Ligne based on depart and arret.
     *
     * @param string $depart
     * @param string $arret
     * @return int|null
     */
    public function findLigneIdByDepartAndArret(string $depart, string $arret): ?int
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r.id')
            ->where('r.depart = :depart')
            ->andWhere('r.arret = :arret')
            ->setParameter('depart', $depart)
            ->setParameter('arret', $arret)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult()['id'] ?? null;
    }
}
