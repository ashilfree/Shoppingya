<?php

namespace App\Repository;

use App\Entity\QuickOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QuickOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuickOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuickOrder[]    findAll()
 * @method QuickOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuickOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuickOrder::class);
    }

    // /**
    //  * @return QuickOrder[] Returns an array of QuickOrder objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QuickOrder
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
