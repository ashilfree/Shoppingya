<?php

namespace App\Controller;

use App\Classes\Cart;
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
     * @return Response
     */
    public function index(Request $request, Cart $cart): Response
    {
        return $this->render('about/index.html.twig', [
            'page' => 'about.us',
            'cart' => $cart->getFull(),
        ]);
    }
}
