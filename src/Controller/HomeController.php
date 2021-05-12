<?php

namespace App\Controller;


use App\Classes\Cart;
use App\Classes\WishList;
use App\Repository\CategoryRepository;
use App\Repository\SlideRepository;
use App\Repository\VisitStatsRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    /**
     * @var VisitStatsRepository
     */
    private $visitStatsRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct( EntityManagerInterface $entityManager,CategoryRepository $categoryRepository, VisitStatsRepository $visitStatsRepository,SlideRepository $slideRepository, Cart $cart, WishList $wishlist)
    {

        $this->slideRepository = $slideRepository;
        $this->cart = $cart;
        $this->categoryRepository = $categoryRepository;
        $this->wishlist = $wishlist;
        $this->visitStatsRepository = $visitStatsRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/{locale}", name="home", defaults={"locale"="en"})
     * @param $locale
     * @return Response
     */
    public function index($locale): Response
    {
        $path = ($locale == "en") ? 'home/index.html.twig' : 'home/indexAr.html.twig';
            return $this->render($path, [
                'page' => 'home',
                'categories' => $this->categoryRepository->findAll(),
                'slides' => $this->slideRepository->findAll(),
                'cart' => $this->cart->getFull($this->cart->get()),
                'wishlist' => $this->wishlist->getFull(),
            ]);

    }
}
