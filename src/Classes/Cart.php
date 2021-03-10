<?php


namespace App\Classes;


use App\Repository\CatalogRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{

	/**
	 * @var SessionInterface
	 */
	private $session;
	private $productRepository;
    /**
     * @var CatalogRepository
     */
    private $catalogRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository, CatalogRepository $catalogRepository)
	{
		$this->session = $session;
		$this->productRepository = $productRepository;
        $this->catalogRepository = $catalogRepository;
    }

	public function add($id, $catalog, $quantity)
	{
		$cart = $this->session->get('cart', []);
		if (empty($cart[$id]) || !in_array($catalog, $cart[$id])) {
			$cart[$id][] = [$catalog, $quantity];
		}
		$this->session->set('cart', $cart);
	}

	public function get()
	{
		return $this->session->get('cart');
	}

    public function getDelivery()
    {
        return $this->session->get('delivery');
    }
	public function getFull()
	{
		$cartComplete = [];
		if (!empty($this->get())) {
			foreach ($this->get() as $id => $catalogs) {
				$cartProduct = $this->productRepository->find($id);
				if (!$cartProduct){
					$this->delete($id);
					continue;
				}
				foreach($catalogs as [$catalog, $quantity]){
                    $cartCatalog = $this->catalogRepository->find($catalog);
                    if (!$cartCatalog){
//                        $this->delete($id);
                        continue;
                    }
                    $cartComplete[] = [
                        'product' => $cartProduct,
                        'catalog' => $cartCatalog,
                        'quantity' => $quantity
                    ];
                }
			}
		}
		return $cartComplete;
	}

	public function remove()
	{
		$this->session->remove('cart');
		$this->session->remove('delivery');
	}

	public function delete($id, $catalog)
	{

		$cart = $this->session->get('cart');

        for($i=0;$i< count($cart[$id]);$i++){
            if ($cart[$id][$i][0] == $catalog)

                unset($cart[$id][$i]);
        }
        if(empty($cart[$id]))
            $this->session->remove('delivery');
		$this->session->set('cart', $cart);
	}

    public function update(array $all)
    {
        $cart = $this->session->get('cart');
        foreach ($all as $key => $value) {
            if (str_contains($key, "product-")) {
                $i = substr($key, -1);
                for($j=0;$j < count($cart[$all["product-".$i]]); $j++){
                    if ($cart[$all["product-".$i]][$j][0] == $all["catalog-".$i]){
                        $cart[$all["product-".$i]][$j][1] =  $all["quantity-".$i];
                    }
                }
            }
        }
        $this->session->set('cart', $cart);
        $this->session->set('delivery',$all['delivery']);
    }

}