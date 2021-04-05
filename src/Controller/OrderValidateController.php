<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Mailer;
use App\Classes\Transaction;
use App\Classes\WishList;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    private $cart;
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var WishList
     */
    private $wishlist;

    public function __construct(EntityManagerInterface $entityManager, Transaction $transaction, Cart $cart, WishList $wishlist, Mailer $mailer)
	{
		$this->entityManager = $entityManager;
		$this->transaction = $transaction;
        $this->cart = $cart;
        $this->mailer = $mailer;
        $this->wishlist = $wishlist;
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
			$this->cart->remove2Order();
			$this->transaction->applyWorkFlow($order, 'checkout');
            $order->setPaidAt(new \DateTime());
			$this->entityManager->flush();
            $this->mailer->sendSuccessOrderEmail($order);
		}

        return $this->render('order/order-complete.html.twig', [
        	'order' => $order,
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'page' => 'order-complete'
        ]);
    }

    /**
     * @Route("/order/error/{stripeSessionId}", name="order.validate.error")
     * @param $stripeSessionId
     * @param Request $request
     * @return Response
     */
	public function cancel($stripeSessionId, Request $request): Response
	{
		$order = $this->entityManager->getRepository(Order::class)->findOneBy(['stripeSessionId' => $stripeSessionId]);
		if(!$order || $order->getCustomer() != $this->getUser()){
			return $this->redirectToRoute('home');
		}
		if ($this->transaction->check($order, 'checkout_canceled'))
			$this->transaction->applyWorkFlow($order, 'checkout_canceled');
        $this->cart->increaseStock();
        $this->cart->remove2Order();
        $order->setCancelledAt(new \DateTime());
		$this->entityManager->flush();
        $this->mailer->sendFailureOrderEmail($order);
        dd($request);
		return $this->render('order/order-canceled.html.twig', [
			'order' => $order,
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'page' => 'order-canceled'
		]);
	}
}
