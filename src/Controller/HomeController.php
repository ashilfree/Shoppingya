<?php

namespace App\Controller;


use App\Classes\Cart;
use App\Classes\WishList;
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
    /**
     * @var WishList
     */
    private $wishlist;

    public function __construct(CategoryRepository $categoryRepository, SlideRepository $slideRepository, Cart $cart, WishList $wishlist)
    {

        $this->slideRepository = $slideRepository;
        $this->cart = $cart;
        $this->categoryRepository = $categoryRepository;
        $this->wishlist = $wishlist;
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
            'wishlist' => $this->wishlist->getFull(),
        ]);
    }
}
