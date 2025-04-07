<?php

namespace App\Repository;

use App\Entity\Reponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reponse::class);
    }


    public function findReclamationsWithReponses(): array
{
    return $this->createQueryBuilder('r')
        ->select('r.id')
        ->innerJoin('r.reponses', 'rep')
        ->groupBy('r.id')
        ->getQuery()
        ->getResult();
}
}
