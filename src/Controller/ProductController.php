<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var Cart
     */
    private $cart;

    public function __construct(CategoryRepository $categoryRepository, ProductRepository $productRepository, Cart $cart)
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->cart = $cart;
    }

    /**
     * @Route("/products", name="products")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->render('product/index.html.twig', [
            'page' => 'products',
            'categories' => $this->categoryRepository->findAll(),
            'products' => $this->productRepository->findAll(),
            'cart' => $this->cart->getFull()
        ]);
    }

    /**
     * @Route("/product/{slug}", name="product")
     * @param $slug
     * @return Response
     */
    public function show($slug): Response
    {
        $product = $this->productRepository->findOneBy(['slug'=>$slug]);
        if(!$product)
            return $this->redirectToRoute('products');

        return $this->render('product/detail.html.twig', [
            'page' => 'detail',
            'product' => $product,
        ]);
    }
}
