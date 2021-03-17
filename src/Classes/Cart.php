<?php


namespace App\Classes;

use App\Entity\Order;
use App\Repository\CatalogRepository;
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

    public function getCart2Order()
    {
        return $this->session->get('cart2order');
    }

    public function getDelivery()
    {
        return $this->session->get('delivery');
    }

    public function getDelivery2Order()
    {
        return $this->session->get('delivery2order');
    }

    public function getFull($cart)
    {
        $cartComplete = [];
        if (!empty($cart)) {
            foreach ($cart as $id => $quantity) {
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

    public function remove2Order()
    {
        $this->session->remove('cart2order');
        $this->session->remove('delivery2order');
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

    public function switch()
    {
        $cart = $this->get();
        $delivery = $this->getDelivery();
        $this->session->set('cart2order', $cart);
        $this->session->set('delivery2order', $delivery);
        $this->remove();
    }

    public function reverseSwitch()
    {
        $cart = $this->getCart2Order();
        $delivery = $this->getDelivery2Order();
        $this->session->set('cart', $cart);
        $this->session->set('delivery', $delivery);
        $this->remove2Order();
    }

    public function checkStock(): bool
    {
        $return = true;
        $cart = $this->getCart2Order();
        if (!empty($cart)) {
            foreach ($cart as $id => $quantity) {
                $cartCatalog = $this->catalogRepository->find($id);
                if ($quantity > $cartCatalog->getQuantity()) {
                    $return = false;
                    break;
                }
            }
        }
        return $return;
    }

    public function decreaseStock()
    {
        $cart = $this->getCart2Order();
        if (!empty($cart)) {
            foreach ($cart as $id => $quantity) {
                $cartCatalog = $this->catalogRepository->find($id);
                $newQuantity = $cartCatalog->getQuantity() - $quantity;
                $cartCatalog->setQuantity($newQuantity);
            }
        }
    }

    public function increaseStock()
    {
        $cart = $this->getCart2Order();
        if (!empty($cart)) {
            foreach ($cart as $id => $quantity) {
                $cartCatalog = $this->catalogRepository->find($id);
                $newQuantity = $cartCatalog->getQuantity() + $quantity;
                $cartCatalog->setQuantity($newQuantity);
            }
        }
    }

    public function createCart2Order(Order $order)
    {
        $cart = [];
        foreach ($order->getOrderDetails() as $orderDetail){
            $catalog = $this->catalogRepository->findByProductName($orderDetail->getProduct(), $orderDetail->getSize());
            $cart[$catalog->getId()] = $orderDetail->getQuantity();
        }
        $this->session->set('cart2order', $cart);
        $this->session->set('delivery2order', $order->getDeliveryPrice());
    }
}