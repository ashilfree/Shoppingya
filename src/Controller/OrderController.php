<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Transaction;
use App\Entity\Address;
use App\Entity\Carrier;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\User;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OrderController
 * @package App\Controller
 * @Route("/order")
 */
class OrderController extends AbstractController
{

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	public function __construct
	(
		EntityManagerInterface $entityManager
	)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @Route("/", name="order")
	 * @param Cart $cart
	 * @param Request $request
	 * @return Response
	 */
	public function index(Cart $cart, Request $request): Response
	{
		if (!$this->getUser()->getAddresses()->getValues())
			return $this->redirectToRoute('account.address.add');

		$form = $this->createForm(OrderType::class, null, [
			'user' => $this->getUser()
		]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			dd($form->getData());
		}

		return $this->render('order/index.html.twig', [
			'form' => $form->createView(),
			'cart' => $cart->getFull()
		]);
	}

	/**
	 * @Route("/recap", name="order.recap", methods={"POST"})
	 * @param Cart $cart
	 * @param Request $request
	 * @param Transaction $transaction
	 * @return Response
	 */
	public function add(Cart $cart, Request $request, Transaction $transaction): Response
	{

		$form = $this->createForm(OrderType::class, null, [
			'user' => $this->getUser()
		]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			//	dd($form->getData());
			$date = new \DateTime();
			/** @var User $user */
			$user = $this->getUser();
			/** @var Carrier $carrier */
			$carrier = $form->get('carriers')->getData();
			/** @var Address $address */
			$address = $form->get('addresses')->getData();
			$delivery_address = $address->getFirstname() . ' ' . $address->getLastname();
			$delivery_address .= '<br/>' . $address->getPhone();
			if ($address->getCompany())
				$delivery_address .= '<br/>' . $address->getCompany();
			$delivery_address .= '<br/>' . $address->getAddress();
			$delivery_address .= '<br/>' . $address->getPostal() . ' ' . $address->getCity();
			$delivery_address .= '<br/>' . $address->getCountry();

			$order = new order();
			$reference = $date->format('Ymd').'-'.uniqid();
			$order->setReference($reference);
			$order->setUser($user);
			$order->setCreatedAt($date);
			$order->setCarrierName($carrier->getName());
			$order->setCarrierPrice($carrier->getPrice());
			$order->setDelivery($delivery_address);
			$transaction->applyWorkFlow($order, 'create_order');
			$this->entityManager->persist($order);


			foreach ($cart->getFull() as $product) {
				$orderDetail = new OrderDetails();
				$orderDetail->setMyOrder($order);
				$orderDetail->setProduct($product['product']->getName());
				$orderDetail->setQuantity($product['quantity']);
				$orderDetail->setPrice($product['product']->getPrice());
				$orderDetail->setTotal($product['quantity'] * $product['product']->getPrice());
				$this->entityManager->persist($orderDetail);
			}
			 $this->entityManager->flush();




			return $this->render('order/add.html.twig', [
					'cart' => $cart->getFull(),
					'carrier' => $carrier,
					'delivery'=> $delivery_address,
					'id'=> $order->getId()
				]
			);

		}

		return  $this->redirectToRoute('cart');

	}
}
