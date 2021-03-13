<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Transaction;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderValidateController extends AbstractController
{

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;
	/**
	 * @var Transaction
	 */
	private $transaction;
    /**
     * @var Cart
     */
    private Cart $cart;

    public function __construct(EntityManagerInterface $entityManager, Transaction $transaction, Cart $cart)
	{
		$this->entityManager = $entityManager;
		$this->transaction = $transaction;
        $this->cart = $cart;
    }

	/**
	 * @Route("/order/thank/{stripeSessionId}", name="order.validate.thank")
	 * @param $stripeSessionId
	 * @return Response
	 */
    public function success($stripeSessionId): Response
    {
	    $order = $this->entityManager->getRepository(Order::class)->findOneBy(['stripeSessionId' => $stripeSessionId]);
		if(!$order || $order->getUser() != $this->getUser()){
			return $this->redirectToRoute('home');
		}

		if ($this->transaction->check($order, 'checkout')){
			$this->cart->remove();
			$this->transaction->applyWorkFlow($order, 'checkout');
			$this->entityManager->flush();
		}

        return $this->render('order/order-complete.html.twig', [
        	'order' => $order,
            'cart' => $this->cart->getFull(),
            'page' => 'order-complete'
        ]);
    }

	/**
	 * @Route("/order/error/{stripeSessionId}", name="order.validate.error")
	 * @param $stripeSessionId
	 * @return Response
	 */
	public function cancel($stripeSessionId): Response
	{
		$order = $this->entityManager->getRepository(Order::class)->findOneBy(['stripeSessionId' => $stripeSessionId]);
		if(!$order || $order->getCustomer() != $this->getUser()){
			return $this->redirectToRoute('home');
		}
		if ($this->transaction->check($order, 'checkout_canceled'))
			$this->transaction->applyWorkFlow($order, 'checkout_canceled');
		$this->entityManager->flush();
		return $this->render('order/order-canceled.html.twig', [
			'order' => $order,
            'cart' => $this->cart->getFull(),
            'page' => 'order-canceled'
		]);
	}
}
