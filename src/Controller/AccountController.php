<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\EditPasswordType;
use App\Form\EditProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AccountController
 * @package App\Controller
 * @Route("/account")
 */
class AccountController extends AbstractController
{

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;
    /**
     * @var Cart
     */
    private $cart;

    public function __construct(EntityManagerInterface $entityManager, Cart $cart)
	{
		$this->entityManager = $entityManager;
        $this->cart = $cart;
    }

	/**
	 * @Route("/", name="account")
	 */
	public function index(): Response
	{
        /**
         * @var Customer $customer
         */
        $customer = $this->getUser();
		return $this->render('account/index.html.twig', [
            'page' => 'account',
            'cart' => $this->cart->getFull($this->cart->get()),
            'customer' => $customer
        ]);
	}

    /**
     * @Route("/edit-account", name="edit.account")
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request): Response
    {
        $customer = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $customer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('account');
        }
        return $this->render('account/edit_profile.html.twig', [
            'page' => 'edit.account',
            'form' => $form->createView(),
            'cart' => $this->cart->getFull($this->cart->get()),
            'customer' => $customer
        ]);
    }

    /**
     * @Route("/my-orders", name="my.orders")
     */
    public function myOrders(): Response
    {
        /**
         * @var Customer $customer
         */
        $customer = $this->getUser();
        $orders = $this->entityManager->getRepository(Order::class)->findBy(['customer' => $customer]);
        return $this->render('account/my_orders.html.twig', [
            'page' => 'my.orders',
            'cart' => $this->cart->getFull($this->cart->get()),
            'customer' => $customer,
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/my-order-detail/{id}", name="my.order.detail")
     * @param Order $order
     * @return Response
     */
    public function myOrderDetail(Order $order): Response
    {
        $orderDetails = $this->entityManager->getRepository(OrderDetails::class)->findBy(['myOrder' => $order]);
        return $this->render('account/my_order_detail.html.twig', [
            'page' => 'my.order.detail',
            'cart' => $this->cart->getFull($this->cart->get()),
            'order' => $order,
            'orderDetails' => $orderDetails,
            'customer' => $this->getUser()
        ]);
    }

    /**
     * @Route("/edit-password", name="edit.password")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function editPassword(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $old_password = $form->get('old_password')->getData();
            if ($encoder->isPasswordValid($user, $old_password)) {
                $new_password = $form->get('new_password')->getData();
                $password = $encoder->encodePassword($user, $new_password);
                $user->setPassword($password);
                $this->entityManager->flush();
                $this->addFlash(
                    'notice',
                    'Your changes were saved!'
                );
            } else {
                $this->addFlash(
                    'notice',
                    'Check your password'
                );
            }
        }
        return $this->render('account/edit_password.html.twig', [
            'page' => 'edit.password',
            'form' => $form->createView(),
            'cart' => $this->cart->getFull($this->cart->get()),
            'customer' => $this->getUser()
        ]);
    }

//
//	/**
//	 * @Route("/my-orders", name="account.my.orders")
//	 */
//	public function myOrders()
//	{
//		$orders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($this->getUser());
//		return $this->render('account/orders.html.twig', [
//			'orders' => $orders
//		]);
//	}
//
//	/**
//	 * @Route("/order-detail/{id}", name="account.order.detail")
//	 * @return Response
//	 */
//	public function showOrder($id)
//	{
//		$order = $this->entityManager->getRepository(Order::class)->find($id);
//		if (!$order || $order->getUser() != $this->getUser()){
//			return $this->redirectToRoute('home');
//		}
//
//		return $this->render('account/order_detail.html.twig', [
//			'order' => $order
//		]);
//	}
}
