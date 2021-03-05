<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
	/**
	 * @var Cart
	 */
	private $cart;
	/**
	 * @var ProductRepository
	 */
	private $productRepository;

	public function __construct(Cart $cart, ProductRepository $productRepository)
	{
		$this->cart = $cart;
		$this->productRepository = $productRepository;
	}

	/**
	 * @Route("/cart", name="cart")
	 */
	public function index(): Response
	{
		return $this->render('cart/index.html.twig', [
			'cart' => $this->cart->getFull()
		]);
	}

	/**
	 * @Route("/cart/add/{id}", name="add.cart")
	 * @param $id
	 * @return Response
	 */
	public function add($id): Response
	{
		$this->cart->add($id);
		return $this->redirectToRoute('cart');
	}

	/**
	 * @Route("/cart/remove", name="remove.cart")
	 * @return Response
	 */
	public function remove(): Response
	{
		$this->cart->remove();
		return $this->redirectToRoute('products');
	}

	/**
	 * @Route("/cart/delete/{id}", name="delete.cart")
	 * @param $id
	 * @return Response
	 */
	public function delete($id): Response
	{
		$this->cart->delete($id);
		return $this->redirectToRoute('cart');
	}

	/**
	 * @Route("/cart/decrease/{id}", name="decrease.cart")
	 * @param $id
	 * @return Response
	 */
	public function decrease($id): Response
	{
		$this->cart->decrease($id);
		return $this->redirectToRoute('cart');
	}
}
