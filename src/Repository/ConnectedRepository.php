<?php

namespace App\Repository;

use App\Entity\Connected;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Connected|null find($id, $lockMode = null, $lockVersion = null)
 * @method Connected|null findOneBy(array $criteria, array $orderBy = null)
 * @method Connected[]    findAll()
 * @method Connected[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConnectedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Connected::class);
    }

    public function deleteLess5Min($value)
    {
        return $this->createQueryBuilder('c')
            ->delete('App:Connected','c')
            ->andWhere('c.timestamp < :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->execute()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Connected
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
