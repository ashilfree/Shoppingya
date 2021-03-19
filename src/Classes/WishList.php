<?php


namespace App\Classes;


use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class WishList
{

    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var ProductRepository
     */
    private $productRepository;


    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;

        $this->productRepository = $productRepository;
    }

    public function add($id)
    {
        $wishlist = $this->session->get('wishlist', []);
        if (empty($wishlist[$id])) {
            $wishlist[$id] = 1;
        }
        $this->session->set('wishlist', $wishlist);
    }

    public function get()
    {
        return $this->session->get('wishlist');
    }

    public function getFull(): array
    {
        $wishlist = $this->get();
        $wishlistComplete = [];
        if (!empty($wishlist)) {
            foreach ($wishlist as $id => $quantity) {
                $wishlistProduct = $this->productRepository->find($id);
                if (!$wishlistProduct) {
                    $this->delete($id);
                    continue;
                }
                $wishlistComplete[] = [
                    'product' => $wishlistProduct,
                ];

            }
        }
        return $wishlistComplete;
    }

    public function remove()
    {
        $this->session->remove('wishlist');
    }

    public function delete($id)
    {

        $wishlist = $this->session->get('wishlist');

        unset($wishlist[$id]);

        $this->session->set('wishlist', $wishlist);
    }

}