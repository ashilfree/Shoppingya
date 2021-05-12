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
     * @Route("/{locale}/cart", name="cart", defaults={"locale"="en"})
     * @param $locale
     * @return Response
     */
    public function index($locale): Response
    {
        $cart = $this->cart->getFull($this->cart->get());
        if (empty($cart)) {
            $path = ($locale == "en") ? 'cart/empty-cart.html.twig' : 'cart/empty-cartAr.html.twig';
            return $this->render($path, [
                'cart' => $cart,
                'wishlist' => $this->wishlist->getFull(),
            ]);
        }else {
            $path = ($locale == "en") ? 'cart/index.html.twig' : 'cart/indexAr.html.twig';
            return $this->render($path, [
                'cart' => $cart,
                'wishlist' => $this->wishlist->getFull(),
                'page' => 'cart',
                'delivery' => $this->cart->getDelivery(),
                'governorates' => $this->governorateRepository->findAll()
            ]);
        }
    }

    /**
     * @Route("/{locale}/cart/add/{id}", name="add.cart", defaults={"id"=0, "locale"="en"})
     * @param $locale
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function add($locale, $id, Request $request): Response
    {
        $this->cart->add($id);
        return $this->redirectToRoute('products', ["locale" => $locale]);
    }

    /**
     * @Route("/{locale}/cart/update", name="update.cart", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @return Response
     */
    public function update($locale, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $this->cart->update($data);
        return $this->redirectToRoute('cart', ["locale" => $locale]);
    }

    /**
     * @Route("/{locale}/cart/remove/{route}", name="remove.cart", defaults={"locale"="en"})
     * @param $locale
     * @param $route
     * @return Response
     */
    public function remove($locale, $route): Response
    {
        $this->cart->remove();
        return $this->redirectToRoute($route, ["locale" => $locale]);
    }

    /**
     * @Route("/{locale}/cart/delete/{id}-{route}", name="delete.cart", defaults={"locale"="en"})
     * @param $locale
     * @param $id
     * @param $route
     * @return Response
     */
    public function delete($locale, $id, $route): Response
    {
        $this->cart->delete($id);
        return $this->redirectToRoute($route, ["locale" => $locale]);
    }
}
