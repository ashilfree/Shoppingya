<?php


namespace App\Classes;


use App\Repository\CatalogRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var CatalogRepository
     */
    private $catalogRepository;

    public function __construct(SessionInterface $session, CatalogRepository $catalogRepository)
    {
        $this->session = $session;
        $this->catalogRepository = $catalogRepository;
    }

    public function add($id)
    {
        $cart = $this->session->get('cart', []);
        if (empty($cart[$id])) {
            $cart[$id] = 1;
        }
        $this->session->set('cart', $cart);
    }

    public function get()
    {
        return $this->session->get('cart');
    }

    public function getDelivery()
    {
        return $this->session->get('delivery');
    }

    public function getFull()
    {
        $cartComplete = [];
        if (!empty($this->get())) {
            foreach ($this->get() as $id => $quantity) {
                $cartCatalog = $this->catalogRepository->find($id);
                if (!$cartCatalog) {
                    $this->delete($id);
                    continue;
                }
                $cartComplete[] = [
                    'catalog' => $cartCatalog,
                    'quantity' => $quantity
                ];

            }
        }
        return $cartComplete;
    }

    public function remove()
    {
        $this->session->remove('cart');
        $this->session->remove('delivery');
    }

    public function delete($id)
    {

        $cart = $this->session->get('cart');

        unset($cart[$id]);

        if (empty($cart[$id]))
            $this->session->remove('delivery');

        $this->session->set('cart', $cart);
    }

    public function update(array $all)
    {
        $cart = $this->session->get('cart');
        foreach ($all as $key => $value) {
            if (str_contains($key, "catalog-")) {
                $i = substr($key, -1);
                $cart[$all["catalog-" . $i]] = $all["quantity-" . $i];
            }
        }
        $this->session->set('cart', $cart);
        $this->session->set('delivery', $all['delivery']);
    }

}