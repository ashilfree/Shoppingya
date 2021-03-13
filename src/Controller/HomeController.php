<?php

namespace App\Controller;


use App\Classes\Cart;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
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
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository, SlideRepository $slideRepository, Cart $cart)
    {

        $this->slideRepository = $slideRepository;
        $this->cart = $cart;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'page' => 'home',
            'categories' => $this->categoryRepository->findAll(),
            'slides' => $this->slideRepository->findAll(),
            'cart' => $this->cart->getFull($this->cart->get()),
        ]);
    }
}
