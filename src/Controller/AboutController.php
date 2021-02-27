<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController
{

    /**
     * @Route("/about-us", name="about.us")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->render('about/index.html.twig', [
            'page' => 'about'
        ]);
    }
}
