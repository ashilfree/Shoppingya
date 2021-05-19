<?php

namespace App\Classes;

use App\Repository\CatalogRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderCleanup{

    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var CatalogRepository
     */
    private $catalogRepository;
    /**
     * @var Transaction
     */
    private $transaction;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, OrderRepository $orderRepository, CatalogRepository $catalogRepository, Transaction $transaction)
    {
        $this->orderRepository = $orderRepository;
        $this->catalogRepository = $catalogRepository;
        $this->transaction = $transaction;
        $this->entityManager = $entityManager;
    }

    public function clean(): int
    {
        $date = (new \DateTime());
//        $date->setTimezone(new DateTimeZone('Asia/Kuwait'));
        $date->sub(new \DateInterval('PT5M'));

        $orders = $this->orderRepository->getOrdersToClean($date);


        foreach ($orders as $order){
            if($this->transaction->check($order, 'order_canceled2'))
                $this->transaction->applyWorkFlow($order, 'order_canceled2');
            if($this->transaction->check($order, 'order_canceled'))
                $this->transaction->applyWorkFlow($order, 'order_canceled');
            foreach ($order->getOrderDetails() as $orderDetail){
                $catalog = $this->catalogRepository->findByProductName($orderDetail->getProduct(), $orderDetail->getSize());
                $newQuantity = $catalog->getQuantity() + $orderDetail->getQuantity();
                $catalog->setQuantity($newQuantity);
            }
            $this->entityManager->flush();
        }

        return count($orders);
    }
}