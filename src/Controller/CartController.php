<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\WishList;
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
    /**
     * @var WishList
     */
    private $wishlist;


    public function __construct(Cart $cart, WishList $wishlist, GovernorateRepository $governorateRepository)
    {
        $this->cart = $cart;
        $this->governorateRepository = $governorateRepository;
        $this->wishlist = $wishlist;
    }

    /**
     * @Route("/cart", name="cart")
     * @return Response
     */
    public function index(): Response
    {
        $cart = $this->cart->getFull($this->cart->get());
        if (empty($cart))
            return $this->render('cart/empty-cart.html.twig', [
                'cart' => $cart,
                'wishlist' => $this->wishlist->getFull(),
            ]);
        else
            return $this->render('cart/index.html.twig', [
                'cart' => $cart,
                'wishlist' => $this->wishlist->getFull(),
                'page' => 'cart',
                'delivery' => $this->cart->getDelivery(),
                'governorates' => $this->governorateRepository->findAll()
            ]);
    }

    /**
     * @Route("/cart-ar", name="cart.ar")
     * @return Response
     */
    public function indexَAr(): Response
    {
        $cart = $this->cart->getFull($this->cart->get());
        if (empty($cart))
            return $this->render('cart/empty-cartAr.html.twig', [
                'cart' => $cart,
                'wishlist' => $this->wishlist->getFull(),
            ]);
        else
            return $this->render('cart/indexAr.html.twig', [
                'cart' => $cart,
                'wishlist' => $this->wishlist->getFull(),
                'page' => 'cart.ar',
                'delivery' => $this->cart->getDelivery(),
                'governorates' => $this->governorateRepository->findAll()
            ]);
    }

    /**
     * @Route("/cart/add/{id}", name="add.cart", defaults={"id"=0})
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function add($id, Request $request): Response
    {
        $this->cart->add($id);
        if(substr($request->server->get("HTTP_REFERER"), -2) == "ar"){
            return $this->redirectToRoute('products.ar');
        }else{
            return $this->redirectToRoute('products');
        }
    }

    /**
     * @Route("/cart/update/{locale}", name="update.cart", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @return Response
     */
    public function update($locale, Request $request): Response
    {

        $this->cart->update($request->query->all());
        if($locale == "ar"){
            return $this->redirectToRoute('cart.ar', [
                'delivery' => $this->cart->getDelivery()
            ]);
        }else{
            return $this->redirectToRoute('cart', [
                'delivery' => $this->cart->getDelivery()
            ]);
        }

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
     * @Route("/cart/delete/{id}-{route}", name="delete.cart")
     * @param $id
     * @param $route
     * @return Response
     */
    public function delete($id, $route): Response
    {
        $this->cart->delete($id);
        return $this->redirectToRoute($route);
    }
}
