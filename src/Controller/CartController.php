<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Repository\GovernorateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @var Cart
     */
    private $cart;
    /**
     * @var GovernorateRepository
     */
    private $governorateRepository;


    public function __construct(Cart $cart, GovernorateRepository $governorateRepository)
    {
        $this->cart = $cart;
        $this->governorateRepository = $governorateRepository;
    }

    /**
     * @Route("/cart", name="cart")
     * @return Response
     */
    public function index(): Response
    {
        $cart = $this->cart->getFull();
        if (empty($cart))
            return $this->render('cart/empty-cart.html.twig', [
                'cart' => $cart
            ]);
        else
            return $this->render('cart/index.html.twig', [
                'cart' => $cart,
                'page' => 'cart',
                'delivery' => $this->cart->getDelivery(),
                'governorates' => $this->governorateRepository->findAll()
            ]);
    }

    /**
     * @Route("/cart/add/{id}-{catalog}-{quantity}", name="add.cart", defaults={"catalog"=0, "quantity"=1})
     * @param $id
     * @param $catalog
     * @param $quantity
     * @return Response
     */
    public function add($id, $catalog, $quantity): Response
    {
        $this->cart->add($id, $catalog, $quantity);
        return $this->redirectToRoute('products');
    }

    /**
     * @Route("/cart/update", name="update.cart")
     * @param Request $request
     * @return Response
     */
    public function update(Request $request): Response
    {
        $this->cart->update($request->query->all());
       // dd($this->cart->getDelivery());
        return $this->redirectToRoute('cart', [
            'delivery' => $this->cart->getDelivery()
        ]);
    }

    /**
     * @Route("/cart/remove/{route}", name="remove.cart")
     * @param $route
     * @return Response
     */
    public function remove($route): Response
    {
        $this->cart->remove();
        return $this->redirectToRoute($route);
    }

    /**
     * @Route("/cart/delete/{id}-{catalog}-{route}", name="delete.cart")
     * @param $id
     * @param $catalog
     * @param $route
     * @return Response
     */
    public function delete($id, $catalog, $route): Response
    {
        $this->cart->delete($id, $catalog);
        return $this->redirectToRoute($route);
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
