<?php

namespace App\Controller;


use App\Classes\Cart;
use App\Repository\SlideRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @var SlideRepository
     */
    private $slideRepository;
    /**
     * @var Cart
     */
    private $cart;

    public function __construct(SlideRepository $slideRepository, Cart $cart)
    {

        $this->slideRepository = $slideRepository;
        $this->cart = $cart;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'page' => 'home',
            'slides' => $this->slideRepository->findAll(),
            'cart' => $this->cart->getFull(),
        ]);
    }
}
