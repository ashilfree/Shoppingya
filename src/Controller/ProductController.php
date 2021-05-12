<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Filter;
use App\Classes\Search;
use App\Classes\WishList;
use App\Form\FilterArType;
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
     * @Route("/{locale}/products", name="products", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @return Response
     */
    public function index($locale, Request $request): Response
    {
        $filter = new Filter();
        $search = new Search();
        $filter->page = $request->get('page',1);
        $filterType = ($locale == "en") ? $this->createForm(FilterType::class, $filter) : $this->createForm(FilterArType::class, $filter);
        $searchType = $this->createForm(SearchType::class, $search);
        $filterType->handleRequest($request);
        $searchType->handleRequest($request);
        $products = $this->productRepository->findSearch($filter, $search, 8);
        [$min , $max] = $this->productRepository->findMinMax($filter);

        $content = ($locale == "en") ? 'product/products.html.twig' : 'product/productsAr.html.twig';
        $sorting = ($locale == "en") ? 'product/_sorting.html.twig' : 'product/_sortingAr.html.twig';
        $pagination = ($locale == "en") ? 'product/_more.html.twig' : 'product/_moreAr.html.twig';
        if($request->get('ajax')){
            return new JsonResponse([
                "content" => $this->renderView($content, ['products' => $products]),
                'sorting' => $this->renderView($sorting, ['products' => $products]),
                'pagination' => $this->renderView($pagination, ['products' => $products]),
                'pages' => ceil($products->getTotalItemCount() / $products->getItemNumberPerPage()),
                'min' => $min,
                'max' => $max
            ]);
        }
        $path = ($locale == "en") ? 'product/index.html.twig' : 'product/indexAr.html.twig';
        return $this->render($path, [
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
     * @Route("/{locale}/product/{slug}", name="product", defaults={"locale"="en"})
     * @param $locale
     * @param $slug
     * @return Response
     */
    public function show($locale, $slug): Response
    {
        $product = $this->productRepository->findOneBy(['slug'=>$slug]);
        if(!$product)
            return $this->redirectToRoute('products', ['locale' => $locale]);
        $products = $this->productRepository->findBy(['category'=>$product->getCategory()]);
        $path = ($locale == "en") ? 'product/detail.html.twig' : 'product/detailAr.html.twig';
        return $this->render($path, [
            'page' => 'product',
            'product' => $product,
            'products' => $products,
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
        ]);
    }
}
