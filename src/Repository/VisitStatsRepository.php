<?php

namespace App\Repository;

use App\Entity\VisitStats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VisitStats|null find($id, $lockMode = null, $lockVersion = null)
 * @method VisitStats|null findOneBy(array $criteria, array $orderBy = null)
 * @method VisitStats[]    findAll()
 * @method VisitStats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitStatsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VisitStats::class);
    }

     /**
      * @return VisitStats[] Returns an array of VisitStats objects
      */

    public function findToday($value): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.visitAt > :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }

    public function lastWeekVisits($date)
    {
        return $this->createQueryBuilder('v')
            ->select('sum(v.visitNumber), DAY(v.visitAt) AS gBday, MONTH(v.visitAt) AS gBmonth, YEAR(v.visitAt) AS gByear')
            ->where('v.visitAt > :date')
            ->setParameter('date', $date)
            ->groupBy("gBday, gBmonth, gByear")
            ->getQuery()
            ->getScalarResult()
            ;
    }

    public function visitsByYear($year)
    {
        return $this->createQueryBuilder('v')
            ->select('sum(v.visitNumber), MONTH(v.visitAt) AS gBmonth, YEAR(v.visitAt) AS gByear')
            ->where('YEAR(v.visitAt) = :year')
            ->setParameter('year', $year)
            ->groupBy("gBmonth, gByear")
            ->getQuery()
            ->getScalarResult()
            ;
    }
    // /**
    //  * @return VisitStats[] Returns an array of VisitStats objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VisitStats
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
