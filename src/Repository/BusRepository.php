<?php

namespace App\Repository;

use App\Entity\Bus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class BusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bus::class);
    }

    
    public function findByCompagnie(string $compagnie): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.compagnie = :compagnie')
            ->setParameter('compagnie', $compagnie)
            ->getQuery()
            ->getResult();
    }

public function findTrajetByDepartAndArrivee($depart, $arrivee)
{
    return $this->createQueryBuilder('t')
        ->where('t.depart = :depart')
        ->andWhere('t.arret = :arrivee')
        ->setParameters([
            'depart' => $depart,
            'arrivee' => $arrivee
        ])
        ->getQuery()
        ->getOneOrNullResult();
}
public function createSearchQueryBuilder(array $criteria): \Doctrine\ORM\Query
{
    $qb = $this->createQueryBuilder('b')
        ->join('b.vehicule', 'v')  // Jointure Bus -> Vehicule
        ->addSelect('v');  // Including vehicule data

    // Immatriculation filter
    if (!empty($criteria['immatriculation'])) {
        $qb->andWhere('v.immatriculation LIKE :immatriculation')
            ->setParameter('immatriculation', '%' . $criteria['immatriculation'] . '%');
    }

    

    // Capacite filter (greater than or equal)
    if (!empty($criteria['capacite'])) {
        $qb->andWhere('v.capacite >= :capacite')
            ->setParameter('capacite', $criteria['capacite']);
    }

    // Etat filter
    if (!empty($criteria['etat'])) {
        $qb->andWhere('v.etat = :etat')
            ->setParameter('etat', $criteria['etat']);
    }

    

    return $qb->getQuery();
}

}
