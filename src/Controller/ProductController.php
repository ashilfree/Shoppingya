<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Filter;
use App\Classes\Search;
use App\Classes\WishList;
use App\Form\FilterType;
use App\Form\SearchType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
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
    /**
     * @var WishList
     */
    private $wishlist;

    public function __construct(CategoryRepository $categoryRepository, ProductRepository $productRepository, Cart $cart, WishList $wishlist)
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->cart = $cart;
        $this->wishlist = $wishlist;
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
        $products = $this->productRepository->findSearch($filter, $search, 8);
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
            'wishlist' => $this->wishlist->getFull(),
            'wish' => $this->wishlist->get(),
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
        $products = $this->productRepository->findBy(['category'=>$product->getCategory()]);
        return $this->render('product/detail.html.twig', [
            'page' => 'product',
            'product' => $product,
            'products' => $products,
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
        ]);
    }

    /**
     * @Route("/products-ar", name="products.ar")
     * @param Request $request
     * @return Response
     */
    public function indexAr(Request $request): Response
    {
        $filter = new Filter();
        $search = new Search();
        $filter->page = $request->get('page',1);
        $filterType = $this->createForm(FilterType::class, $filter);
        $searchType = $this->createForm(SearchType::class, $search);
        $filterType->handleRequest($request);
        $searchType->handleRequest($request);
        $products = $this->productRepository->findSearch($filter, $search, 8);
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

        return $this->render('product/indexAr.html.twig', [
            'page' => 'products.ar',
            'categories' => $this->categoryRepository->findAll(),
            'products' => $products,
            'filter_form' => $filterType->createView(),
            'search_form' => $searchType->createView(),
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'wish' => $this->wishlist->get(),
            'min' => $min,
            'max' => $max
        ]);
    }

    /**
     * @Route("/product-ar/{slug}", name="product.ar")
     * @param $slug
     * @return Response
     */
    public function showAr($slug): Response
    {
        $product = $this->productRepository->findOneBy(['slug'=>$slug]);
        if(!$product)
            return $this->redirectToRoute('products.ar');
        $products = $this->productRepository->findBy(['category'=>$product->getCategory()]);
        return $this->render('product/detailAr.html.twig', [
            'page' => 'product.ar',
            'product' => $product,
            'products' => $products,
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
        ]);
    }
}
