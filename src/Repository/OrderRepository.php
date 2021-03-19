<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
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
