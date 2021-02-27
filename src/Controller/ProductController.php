<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    /**
     * @Route("/products", name="products")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->render('product/index.html.twig', [
            'page' => 'products'
        ]);
    }
}
