<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\WishList;
use App\Repository\AboutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController
{

    /**
     * @Route("/about-us", name="about.us")
     * @param Request $request
     * @param Cart $cart
     * @param WishList $wishlist
     * @param AboutRepository $aboutRepository
     * @return Response
     */
    public function index(Request $request, Cart $cart, WishList $wishlist, AboutRepository $aboutRepository): Response
    {
        return $this->render('about/index.html.twig', [
            'page' => 'about.us',
            'cart' => $cart->getFull($cart->get()),
            'wishlist' => $wishlist->getFull(),
            'abouts' => $aboutRepository->findAll()
        ]);
    }

    /**
     * @Route("/about-us-ar", name="about.us.ar")
     * @param Request $request
     * @param Cart $cart
     * @param WishList $wishlist
     * @param AboutRepository $aboutRepository
     * @return Response
     */
    public function indexAr(Request $request, Cart $cart, WishList $wishlist, AboutRepository $aboutRepository): Response
    {
        return $this->render('about/indexAr.html.twig', [
            'page' => 'about.us.ar',
            'cart' => $cart->getFull($cart->get()),
            'wishlist' => $wishlist->getFull(),
            'abouts' => $aboutRepository->findAll()
        ]);
    }
}
