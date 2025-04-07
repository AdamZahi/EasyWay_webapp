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
} 