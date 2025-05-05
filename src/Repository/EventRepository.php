<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function search(string $query = null): array
    {
        $qb = $this->createQueryBuilder('e');

        if ($query) {
            $qb
                ->where('e.type LIKE :query')
                ->orWhere('e.status LIKE :query')
                ->orWhere('e.description LIKE :query')
                ->orWhere('e.dateDebut LIKE :query')
                ->orWhere('e.dateFin LIKE :query')
                ->setParameter('query', '%' . $query . '%');
        }

        return $qb
            ->orderBy('e.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getStatistics(): array
{
    $conn = $this->getEntityManager()->getConnection();

    $sql = '
        SELECT 
            type,
            status,
            ligne_affectee,
            COUNT(*) AS total
        FROM evenement
        GROUP BY type, status, ligne_affectee
    ';

    $stmt = $conn->prepare($sql);
    $resultSet = $stmt->executeQuery();

    return $resultSet->fetchAllAssociative();
}

public function findByFilters(array $filters = []): array
{
    $qb = $this->createQueryBuilder('e');

    if (!empty($filters['type'])) {
        $qb->andWhere('e.type = :type')->setParameter('type', $filters['type']);
    }
    if (!empty($filters['status'])) {
        $qb->andWhere('e.status = :status')->setParameter('status', $filters['status']);
    }
    if (!empty($filters['dateDebut'])) {
        $qb->andWhere('e.dateDebut >= :start')->setParameter('start', $filters['dateDebut']);
    }
    if (!empty($filters['dateFin'])) {
        $qb->andWhere('e.dateFin <= :end')->setParameter('end', $filters['dateFin']);
    }

    return $qb->getQuery()->getResult();
}

} 