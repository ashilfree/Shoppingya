<?php

namespace App\Controller;


use App\Classes\Cart;
use App\Classes\WishList;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SlideRepository;
use App\Repository\VisitStatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuickBuyController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, ProductRepository $productRepository)
    {
        $this->entityManager = $entityManager;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/{locale}/quick", name="quick.buy", defaults={"locale"="en"})
     * @param $locale
     * @return Response
     */
    public function index($locale): Response
    {
        $path = ($locale == "en") ? 'quick/index.html.twig' : 'quick/indexAr.html.twig';
            return $this->render($path, [
                'products' => $this->productRepository->findBy([]),
            ]);
    }

    /**
     * @Route("/quick/check-product/{id}", name="quick.check.product")
     * @param $id
     * @return JsonResponse
     */
    public function checkProduct($id): JsonResponse
    {
        /** @var Product $product */
        $product = $this->productRepository->find($id);
        $catalogs = $product->getCatalogs();
        $cats = [];
        foreach($catalogs as $catalog) {
            if($catalog->getQuantity() != 0 && $catalog->getSize() != null)
            $cats[] = [$catalog->getQuantity(), $catalog->getSize()->getName()];
        }
        return new JsonResponse([
            'image' => 'https://localhost:8000/media/images/product/'.$product->getImages()[0]->getFilename(),
            'catalogs' => $cats
        ]);
    }
}
