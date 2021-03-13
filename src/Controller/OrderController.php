<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Transaction;
use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OrderController
 * @package App\Controller
 * @Route("/order")
 */
class OrderController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var Cart
     */
    private $cart;

    public function __construct
    (
        EntityManagerInterface $entityManager,
        Cart $cart
    )
    {
        $this->entityManager = $entityManager;
        $this->cart = $cart;
    }

    /**
     * @Route("/", name="order")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if (!$this->getUser())
            return $this->redirectToRoute('login');
        if(!empty($this->cart->get())){
            $this->cart->switch();
        }
        if (empty($request->request->all())) {
            $order = new Order();
        } else {
            $id = $request->request->get('order')["id"];
            $order = $this->entityManager->getRepository(Order::class)->find($id);
        }
        $form = $this->createForm(OrderType::class, $order);

        return $this->render('order/checkout.html.twig', [
            'form' => $form->createView(),
            'cart' => $this->cart->getFull($this->cart->get()),
            'cart2order' => $this->cart->getFull($this->cart->getCart2Order()),
            'delivery' => $this->cart->getDelivery(),
            'delivery2order' => $this->cart->getDelivery2Order(),
            'page' => 'checkout'
        ]);
    }

    /**
     * @Route("/recap/", name="order.recap", methods={"POST"})
     * @param Request $request
     * @param Transaction $transaction
     * @return Response
     */
    public function add(Request $request, Transaction $transaction): Response
    {

        $id = $request->request->get('order')["id"];
        if ($id == "") {
            $order = new Order();
        } else {
            $order = $this->entityManager->getRepository(Order::class)->find($id);
        }
        $form = $this->createForm(OrderType::class, $order);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if ($order->getId() == null) {
                $date = new \DateTime();
                /** @var Customer $user */
                $user = $this->getUser();
                $reference = $date->format('Ymd') . '-' . uniqid();
                $order->setReference($reference);
                $order->setCustomer($user);
                $order->setCreatedAt($date);
                $order->setDeliveryPrice($this->cart->getDelivery2Order());
                $transaction->applyWorkFlow($order, 'create_order');
                $this->entityManager->persist($order);

                foreach ($this->cart->getFull($this->cart->getCart2Order()) as $product) {
                    $orderDetail = new OrderDetails();
                    $orderDetail->setMyOrder($order);
                    $orderDetail->setProduct($product['catalog']->getProduct()->getName());
                    $orderDetail->setSize($product['catalog']->getSize());
                    $orderDetail->setQuantity($product['quantity']);
                    $orderDetail->setPrice($product['catalog']->getProduct()->getPrice());
                    $orderDetail->setTotal($product['quantity'] * $product['catalog']->getProduct()->getPrice());
                    $this->entityManager->persist($orderDetail);
                }
            }

            $this->entityManager->flush();
            return $this->render('order/checkout-two.html.twig', [
                    'cart' => $this->cart->getFull($this->cart->get()),
                    'cart2order' => $this->cart->getFull($this->cart->getCart2Order()),
                    'order' => $order,
                    'page' => 'checkout-two',
                    'form' => $this->createForm(OrderType::class, $order)->createView()
                ]
            );

        }

        return $this->redirectToRoute('cart');

    }
}
