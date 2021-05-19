<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Mailer;
use App\Classes\Transaction;
use App\Classes\WishList;
use App\Entity\Order;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session, CategoryRepository $categoryRepository, Transaction $transaction, Cart $cart, WishList $wishlist, Mailer $mailer)
	{
		$this->entityManager = $entityManager;
		$this->transaction = $transaction;
        $this->cart = $cart;
        $this->mailer = $mailer;
        $this->wishlist = $wishlist;
        $this->session = $session;
        $this->categoryRepository = $categoryRepository;
    }

	/**
	 * @Route("/{locale}/order/thank/{reference}", name="order.validate.thank")
	 * @param $reference
	 * @return Response
	 */
    public function success($reference): Response
    {
        $locale = $this->session->get("locale");
	    $order = $this->entityManager->getRepository(Order::class)->findOneBy(['reference' => $reference]);
		if(!$order || $order->getCustomer() != $this->getUser()){
			return $this->redirectToRoute('home');
		}

		if ($this->transaction->check($order, 'checkout')){
            $this->session->clear();
			$this->transaction->applyWorkFlow($order, 'checkout');
            $order->setPaidAt(new \DateTime());
			$this->entityManager->flush();
            $this->mailer->sendSuccessOrderEmail($order);
		}
        $path = ($locale == "en") ? 'order/order-complete.html.twig' : 'order/order-completeAr.html.twig';
        return $this->render($path, [
        	'order' => $order,
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'page' => 'order-complete',
            'categories' => $this->categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{locale}/order/error/{reference}", name="order.validate.error")
     * @param $reference
     * @return Response
     */
	public function cancel($reference): Response
	{
        $locale = $this->session->get("locale");
		$order = $this->entityManager->getRepository(Order::class)->findOneBy(['reference' => $reference]);
		if(!$order || $order->getCustomer() != $this->getUser()){
			return $this->redirectToRoute('home');
		}
		if ($this->transaction->check($order, 'checkout_canceled'))
			$this->transaction->applyWorkFlow($order, 'checkout_canceled');
        $this->cart->increaseStock();
        $this->session->clear();
        $order->setCancelledAt(new \DateTime());
		$this->entityManager->flush();
        $this->mailer->sendFailureOrderEmail($order);
        $path = ($locale == "en") ? 'order/order-canceled.html.twig' : 'order/order-canceledAr.html.twig';
		return $this->render($path, [
			'order' => $order,
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'page' => 'order-canceled',
            'categories' => $this->categoryRepository->findAll(),
		]);
	}
}
