<?php

namespace App\Repository;

use App\Entity\Tog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tog[]    findAll()
 * @method Tog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagArRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tog::class);
    }

    // /**
    //  * @return TagAr[] Returns an array of TagAr objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TagAr
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
