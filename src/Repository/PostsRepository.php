<?php

namespace App\Repository;

use App\Entity\Posts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Posts>
 */
class PostsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Posts::class);
    }

    //    /**
    //     * @return Posts[] Returns an array of Posts objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Posts
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    // src/Repository/PostsRepository.php
// src/Repository/PostsRepository.php

public function countByVilleDepart(): array
{
    return $this->createQueryBuilder('p')
        ->select('p.ville_depart AS ville, COUNT(p.id_post) AS total')
        ->groupBy('p.ville_depart')
        ->orderBy('total', 'DESC')
        ->getQuery()
        ->getResult();
}

public function countByVilleArrivee(): array
{
    return $this->createQueryBuilder('p')
        ->select('p.ville_arrivee AS ville, COUNT(p.id_post) AS total')
        ->groupBy('p.ville_arrivee')
        ->orderBy('total', 'DESC')
        ->getQuery()
        ->getResult();
}
public function getAverageSeats(): float
{
    return $this->createQueryBuilder('p')
        ->select('AVG(p.nombreDePlaces) as avgSeats')
        ->getQuery()
        ->getSingleScalarResult();
}

public function getAveragePrice(): float
{
    return $this->createQueryBuilder('p')
        ->select('AVG(p.prix) as avgPrice')
        ->getQuery()
        ->getSingleScalarResult();
}

public function getTotalComments(): int
{
    return $this->createQueryBuilder('p')
        ->select('SUM(SIZE(p.commentaires)) as totalComments')
        ->getQuery()
        ->getSingleScalarResult();
}

public function getMonthlyActivity(): array
{
    $conn = $this->getEntityManager()->getConnection();
    
    // Version compatible avec la plupart des SGBD
    $sql = "
        SELECT 
            EXTRACT(YEAR FROM p.date) as year,
            EXTRACT(MONTH FROM p.date) as month, 
            COUNT(p.id_post) as count
        FROM posts p
        GROUP BY year, month
        ORDER BY year, month ASC
    ";
    
    $stmt = $conn->prepare($sql);
    $result = $stmt->executeQuery();
    $rows = $result->fetchAllAssociative();

    $stats = [];
    foreach ($rows as $row) {
        $monthKey = sprintf("%04d-%02d", $row['year'], $row['month']);
        $stats[$monthKey] = (int)$row['count'];
    }

    return $stats;
}

public function getTopCities(int $limit = 5): array
{
    $results = $this->createQueryBuilder('p')
        ->select('p.ville_depart as city, COUNT(p.id_post) as count')
        ->groupBy('city')
        ->orderBy('count', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();

    $stats = [];
    foreach ($results as $result) {
        $stats[$result['city']] = $result['count'];
    }

    return $stats;
}

public function countByPriceRange(?float $min, ?float $max): int
{
    $qb = $this->createQueryBuilder('p')
        ->select('COUNT(p.id_post)');

    if ($min !== null) {
        $qb->andWhere('p.prix >= :min')
           ->setParameter('min', $min);
    }

    if ($max !== null) {
        $qb->andWhere('p.prix < :max')
           ->setParameter('max', $max);
    }

    return $qb->getQuery()->getSingleScalarResult();
}

public function countBySeats(int $seats): int
{
    return $this->createQueryBuilder('p')
        ->select('COUNT(p.id_post)')
        ->where('p.nombreDePlaces = :seats')
        ->setParameter('seats', $seats)
        ->getQuery()
        ->getSingleScalarResult();
}

public function countBySeatsRange(?int $min, ?int $max): int
{
    $qb = $this->createQueryBuilder('p')
        ->select('COUNT(p.id_post)');

    if ($min !== null) {
        $qb->andWhere('p.nombreDePlaces >= :min')
           ->setParameter('min', $min);
    }

    if ($max !== null) {
        $qb->andWhere('p.nombreDePlaces <= :max')
           ->setParameter('max', $max);
    }

    return $qb->getQuery()->getSingleScalarResult();
}


}
