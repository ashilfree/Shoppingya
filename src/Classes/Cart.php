<?php


namespace App\Classes;


use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
	/**
	 * @var SessionInterface
	 */
	private $session;
	private $productRepository;

	public function __construct(SessionInterface $session, ProductRepository $productRepository)
	{
		$this->session = $session;
		$this->productRepository = $productRepository;
	}

	public function add($id)
	{
		$cart = $this->session->get('cart', []);
		if (empty($cart[$id])) {
			$cart[$id] = [
			    "name" => "name",
                "price" => "price",
                "size" => "size",
                "color" => "color"
            ];
		}
		$this->session->set('cart', $cart);
	}

	public function get()
	{
		return $this->session->get('cart');
	}

	public function getFull()
	{
		$cartComplete = [];
		if (!empty($this->get())) {
			foreach ($this->get() as $id => $quantity) {
				$cartProduct = $this->productRepository->find($id);
				if (!$cartProduct){
					$this->delete($id);
					continue;
				}
				$cartComplete[] = [
					'product' => $cartProduct,
					'quantity' => $quantity
				];
			}
		}
		return $cartComplete;
	}

	public function remove()
	{
		$this->session->remove('cart');
	}

	public function delete($id)
	{
		$cart = $this->session->get('cart');
		unset($cart[$id]);
		$this->session->set('cart', $cart);
	}

	public function decrease($id)
	{
		$cart = $this->session->get('cart');

		if ($cart[$id] > 1) {
			$cart[$id]--;
		} else {
			unset($cart[$id]);
		}
		$this->session->set('cart', $cart);
	}

}