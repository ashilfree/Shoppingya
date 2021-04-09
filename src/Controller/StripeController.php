<?php

namespace App\Controller;

use App\Classes\Transaction;
use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
	/**
	 * @Route("/order/create-session/{id}", name="stripe_create_session")
	 * @param $id
	 * @param EntityManagerInterface $entityManager
	 * @param Transaction $transaction
	 * @return Response
	 * @throws \Stripe\Exception\ApiErrorException
	 */
	public function index($id,EntityManagerInterface $entityManager, Transaction $transaction): Response
	{
		$order = $entityManager->getRepository(Order::class)->find($id);
		if (!$order || !$transaction->check($order, 'proceed_checkout'))
			return new JsonResponse(["error" => 'order']);
		$YOUR_DOMAIN = 'http://127.0.0.1:8000';
		$product_for_stripe = [];

		Stripe::setApiKey('sk_test_51IU51mLg5TZnbiRrGxaUlCa9qamQCo24YESCXZHytvFteAQEkR6WvwcG3ZXTY7IbdSMFEJUMG9PyygazjbOHPTqx00srNcDRob');
		foreach ($order->getOrderDetails()->getValues() as $product) {
			/**
			 * @var Product $product_object
			 */
			$product_object = $entityManager->getRepository(Product::class)->findOneBy(["name" => $product->getProduct()]);
			$product_for_stripe[] = [
				'price_data' => [
					'currency' => 'usd',
					'unit_amount' => $product->getPrice(),
					'product_data' => [
						'name' => $product->getProduct(),
						'images' => [$YOUR_DOMAIN . '/uploads/' . $product_object->getImages()->first()->getFilename()],
					],
				],
				'quantity' => $product->getQuantity(),
			];
		}
		$product_for_stripe[] = [
			'price_data' => [
				'currency' => 'usd',
				'unit_amount' => $order->getDeliveryPrice(),
				'product_data' => [
					'name' => 'Shoppinga Express',
					'images' => [$YOUR_DOMAIN],
				],
			],
			'quantity' => 1,
		];

		$checkout_session = Session::create([
			'customer_email' => $order->getShippingEmail(),
			'payment_method_types' => ['card'],
			'line_items' => [$product_for_stripe],
			'mode' => 'payment',
			'success_url' => $YOUR_DOMAIN . '/order/thank/{CHECKOUT_SESSION_ID}',
			'cancel_url' => $YOUR_DOMAIN . '/order/error/{CHECKOUT_SESSION_ID}',
		]);


		$order->setStripeSessionId($checkout_session->id);
		$transaction->applyWorkFlow($order, 'proceed_checkout');
		$entityManager->flush();
		return new JsonResponse(['id' => $checkout_session->id]);
	}
}
