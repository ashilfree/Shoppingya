<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\WishList;
use App\Repository\GovernorateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WishListController extends AbstractController
{
    /**
     * @var WishList
     */
    private $wishlist;


    public function __construct(WishList $wishlist)
    {
        $this->wishlist = $wishlist;
    }

    /**
     * @Route("/wishlist/add/{id}", name="add.wishlist", defaults={"id"=0})
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function add($id, Request $request): Response
    {
        $this->wishlist->add($id);
        if(substr($request->server->get("HTTP_REFERER"), -2) == "ar"){
            return $this->redirectToRoute('products.ar');
        }else{
            return $this->redirectToRoute('products');
        }
    }

    /**
     * @Route("/wishlist/remove/{route}", name="remove.wishlist")
     * @param $route
     * @return Response
     */
    public function remove($route): Response
    {
        $this->wishlist->remove();
        return $this->redirectToRoute($route);
    }

    /**
     * @Route("/wishlist/delete/{id}-{route}", name="delete.wishlist")
     * @param $id
     * @param $route
     * @return Response
     */
    public function delete($id, $route): Response
    {
        $this->wishlist->delete($id);
        return $this->redirectToRoute($route);
    }
}
