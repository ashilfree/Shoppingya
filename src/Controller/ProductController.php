<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\CartItem;
use App\Classes\Filter;
use App\Classes\Search;
use App\Entity\Product;
use App\Form\CartType;
use App\Form\FilterType;
use App\Form\ProductCartType;
use App\Form\SearchType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $filter = new Filter();
        $search = new Search();
        $filter->page = $request->get('page',1);
        $filterType = $this->createForm(FilterType::class, $filter);
        $searchType = $this->createForm(SearchType::class, $search);
        $filterType->handleRequest($request);
        $searchType->handleRequest($request);
        $products = $this->productRepository->findSearch($filter, $search, 16);
      //  dd($products);
        [$min , $max] = $this->productRepository->findMinMax($filter);

        if($request->get('ajax')){
            return new JsonResponse([
                "content" => $this->renderView('product/products.html.twig', ['products' => $products]),
                'sorting' => $this->renderView('product/_sorting.html.twig', ['products' => $products]),
                'pagination' => $this->renderView('product/_more.html.twig', ['products' => $products]),
                'pages' => ceil($products->getTotalItemCount() / $products->getItemNumberPerPage()),
                'min' => $min,
                'max' => $max
            ]);
        }

        return $this->render('product/index.html.twig', [
            'page' => 'products',
            'categories' => $this->categoryRepository->findAll(),
            'products' => $products,
            'filter_form' => $filterType->createView(),
            'search_form' => $searchType->createView(),
            'cart' => $this->cart->getFull($this->cart->get()),
            'min' => $min,
            'max' => $max
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
            'cart' => $this->cart->getFull($this->cart->get()),
        ]);
    }
}
