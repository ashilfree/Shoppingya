<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Order::class);
        $this->entityManager = $entityManager;
    }

	public function findSuccessOrders($customer)
	{
		return $this->createQueryBuilder('o')
			->orWhere("o.marking like '%paid%'")
			->orWhere("o.marking like '%in_preparation%'")
			->orWhere("o.marking like '%in_delivering%'")
			->orWhere("o.marking like '%delivered%'")
			->andWhere('o.customer = :customer')
			->setParameter('customer', $customer)
			->orderBy('o.id', 'DESC')
			->setMaxResults(10)
			->getQuery()
			->getResult()
			;
    }

    public function findAllSuccessOrders()
    {
        return $this->createQueryBuilder('o')
            ->orWhere("o.marking like '%paid%'")
            ->orWhere("o.marking like '%in_preparation%'")
            ->orWhere("o.marking like '%in_delivering%'")
            ->orWhere("o.marking like '%delivered%'")
            ->orderBy('o.id', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findLastWeekSuccessOrders($date)
    {
        return $this->createQueryBuilder('o')
            ->select('count(o.id), DAY(o.createdAt) AS gBday, MONTH(o.createdAt) AS gBmonth, YEAR(o.createdAt) AS gByear')
            ->andWhere("o.marking like '%paid%' or o.marking like '%in_preparation%' or o.marking like '%in_delivering%' or o.marking like '%delivered%'")
            ->andWhere('o.createdAt > :date')
            ->setParameter('date', $date)
            ->groupBy("gBday, gBmonth, gByear")
            ->getQuery()
            ->getResult()
            ;
    }

    public function findPendingOrders($customer)
    {
        return $this->createQueryBuilder('o')
            ->orWhere("o.marking like '%waiting%'")
            ->orWhere("o.marking like '%in_payment%'")
            ->andWhere('o.customer = :customer')
            ->setParameter('customer', $customer)
            ->orderBy('o.id', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllPendingOrders()
    {
        return $this->createQueryBuilder('o')
            ->orWhere("o.marking like '%waiting%'")
            ->orWhere("o.marking like '%in_payment%'")
            ->orderBy('o.id', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findLastWeekPendingOrders($date)
    {
        return $this->createQueryBuilder('o')
            ->select('count(o.id), DAY(o.createdAt) AS gBday, MONTH(o.createdAt) AS gBmonth, YEAR(o.createdAt) AS gByear')
            ->andWhere("o.marking like '%waiting%' or o.marking like '%in_payment%'")
            ->andWhere('o.createdAt > :date')
            ->setParameter('date', $date)
            ->groupBy("gBday, gBmonth, gByear")
            ->getQuery()
            ->getResult()
            ;
    }

    public function findCanceledOrders($customer)
    {
        return $this->createQueryBuilder('o')
            ->orWhere("o.marking like '%checkout_canceled%'")
            ->orWhere("o.marking like '%canceled%'")
            ->andWhere('o.customer = :customer')
            ->setParameter('customer', $customer)
            ->orderBy('o.id', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllCanceledOrders()
    {
        return $this->createQueryBuilder('o')
            ->orWhere("o.marking like '%checkout_canceled%'")
            ->orWhere("o.marking like '%canceled%'")
            ->orderBy('o.id', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findLastWeekCanceledOrders($date)
    {
        return $this->createQueryBuilder('o')
            ->select('count(o.id), DAY(o.createdAt) AS gBday, MONTH(o.createdAt) AS gBmonth, YEAR(o.createdAt) AS gByear')
            ->andWhere("o.marking like '%checkout_canceled%' or o.marking like '%canceled%'")
            ->andWhere('o.createdAt > :date')
            ->setParameter('date', $date)
            ->groupBy("gBday, gBmonth, gByear")
            ->getQuery()
            ->getResult()
            ;
    }

    public function lastWeekOrders($date)
    {
        return $this->createQueryBuilder('o')
            ->select('count(o.id), DAY(o.createdAt) AS gBday, MONTH(o.createdAt) AS gBmonth, YEAR(o.createdAt) AS gByear')
            ->where('o.createdAt > :date')
            ->setParameter('date', $date)
            ->groupBy("gBday, gBmonth, gByear")
            ->getQuery()
            ->getScalarResult()
            ;
    }

    public function ordersByYear($year)
    {
        return $this->createQueryBuilder('o')
            ->select('count(o.id), MONTH(o.createdAt) AS gBmonth, YEAR(o.createdAt) AS gByear')
            ->where('YEAR(o.createdAt) = :year')
            ->setParameter('year', $year)
            ->groupBy("gBmonth, gByear")
            ->getQuery()
            ->getScalarResult()
            ;
    }

    public function getOrdersToClean($date)
    {
        return $this->createQueryBuilder('o')
            ->where('o.createdAt < :date')
            ->setParameter('date', $date)
            ->andWhere("o.marking like '%waiting%'")
            ->orWhere("o.marking like '%in_payment%'")
            ->orderBy('o.id', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Order[] Returns an array of Order objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
