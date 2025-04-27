<?php

namespace App\Repository;

use App\Entity\Conducteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conducteur>
 */
class ConducteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conducteur::class);
    }

//    /**
//     * @return Conducteur[] Returns an array of Conducteur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Conducteur
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function findIdsByNomPrenom(string $search): array
{
    $qb = $this->createQueryBuilder('c')
        ->select('c.id')
        ->where('c.nom LIKE :search')
        ->orWhere('c.prenom LIKE :search')
        ->setParameter('search', '%' . $search . '%');

    $results = $qb->getQuery()->getArrayResult();
    
    // Just extract the IDs into a flat array
    return array_map(fn($row) => $row['id'], $results);
}
}
