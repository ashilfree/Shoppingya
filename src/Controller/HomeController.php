<?php

namespace App\Controller;


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

    public function __construct(SlideRepository $slideRepository)
    {

        $this->slideRepository = $slideRepository;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'page' => 'home',
            'slides' => $this->slideRepository->findAll()
        ]);
    }
}
