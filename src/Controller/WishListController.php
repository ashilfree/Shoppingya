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
     * @Route("/{locale}/wishlist/add/{id}", name="add.wishlist", defaults={"id"=0, "locale"="en"})
     * @param $locale
     * @param $id
     * @return Response
     */
    public function add($locale, $id): Response
    {
        $this->wishlist->add($id);
        return $this->redirectToRoute('products', ['label' => $locale]);
    }

    /**
     * @Route("/{locale}/wishlist/remove/{route}", name="remove.wishlist", defaults={"locale"="en"})
     * @param $locale
     * @param $route
     * @return Response
     */
    public function remove($locale, $route): Response
    {
        $this->wishlist->remove();
        return $this->redirectToRoute($route, ['locale' => $locale]);
    }

    /**
     * @Route("/{locale}/wishlist/delete/{id}-{route}", name="delete.wishlist", defaults={"locale"="en"})
     * @param $locale
     * @param $id
     * @param $route
     * @return Response
     */
    public function delete($locale, $id, $route): Response
    {
        $this->wishlist->delete($id);
        return $this->redirectToRoute($route, ['locale' => $locale]);
    }
}
