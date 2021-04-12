<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\WishList;
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
    /**
     * @var WishList
     */
    private $wishlist;

    public function __construct(EntityManagerInterface $entityManager, Cart $cart, WishList $wishlist)
	{
		$this->entityManager = $entityManager;
        $this->cart = $cart;
        $this->wishlist = $wishlist;
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
        $pendingOrders = $this->entityManager->getRepository(Order::class)->findPendingOrders( $customer);
        $successOrders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($customer);
        $canceledOrders = $this->entityManager->getRepository(Order::class)->findCanceledOrders($customer);
		return $this->render('account/index.html.twig', [
            'page' => 'account',
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'customer' => $customer,
            'pendingOrders' => $pendingOrders,
            'successOrders' => $successOrders,
            'canceledOrders' => $canceledOrders,
        ]);
	}

    /**
     * @Route("/ar", name="account.ar")
     */
    public function indexAr(): Response
    {
        /**
         * @var Customer $customer
         */
        $customer = $this->getUser();
        $pendingOrders = $this->entityManager->getRepository(Order::class)->findPendingOrders( $customer);
        $successOrders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($customer);
        $canceledOrders = $this->entityManager->getRepository(Order::class)->findCanceledOrders($customer);
        return $this->render('account/indexAr.html.twig', [
            'page' => 'account.ar',
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'customer' => $customer,
            'pendingOrders' => $pendingOrders,
            'successOrders' => $successOrders,
            'canceledOrders' => $canceledOrders,
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
        $pendingOrders = $this->entityManager->getRepository(Order::class)->findPendingOrders( $customer);
        $successOrders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($customer);
        $canceledOrders = $this->entityManager->getRepository(Order::class)->findCanceledOrders($customer);
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
            'wishlist' => $this->wishlist->getFull(),
            'customer' => $customer,
            'pendingOrders' => $pendingOrders,
            'successOrders' => $successOrders,
            'canceledOrders' => $canceledOrders,
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
        $pendingOrders = $this->entityManager->getRepository(Order::class)->findPendingOrders( $customer);
        $successOrders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($customer);
        $canceledOrders = $this->entityManager->getRepository(Order::class)->findCanceledOrders($customer);
        return $this->render('account/my_orders.html.twig', [
            'page' => 'my.orders',
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'customer' => $customer,
            'orders' => $orders,
            'pendingOrders' => $pendingOrders,
            'successOrders' => $successOrders,
            'canceledOrders' => $canceledOrders,
        ]);
    }

    /**
     * @Route("/my-order-detail/{id}", name="my.order.detail")
     * @param Order $order
     * @return Response
     */
    public function myOrderDetail(Order $order): Response
    {
        $customer = $this->getUser();
        if (!$order || $order->getCustomer() != $customer){
			return $this->redirectToRoute('home');
		}
        $orderDetails = $this->entityManager->getRepository(OrderDetails::class)->findBy(['myOrder' => $order]);
        $pendingOrders = $this->entityManager->getRepository(Order::class)->findPendingOrders( $customer);
        $successOrders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($customer);
        $canceledOrders = $this->entityManager->getRepository(Order::class)->findCanceledOrders($customer);
        return $this->render('account/my_order_detail.html.twig', [
            'page' => 'my.order.detail',
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'order' => $order,
            'orderDetails' => $orderDetails,
            'customer' => $customer,
            'pendingOrders' => $pendingOrders,
            'successOrders' => $successOrders,
            'canceledOrders' => $canceledOrders,
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
        $customer = $this->getUser();
        $pendingOrders = $this->entityManager->getRepository(Order::class)->findPendingOrders( $customer);
        $successOrders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($customer);
        $canceledOrders = $this->entityManager->getRepository(Order::class)->findCanceledOrders($customer);
        $form = $this->createForm(EditPasswordType::class, $customer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $old_password = $form->get('old_password')->getData();
            if ($encoder->isPasswordValid($customer, $old_password)) {
                $new_password = $form->get('new_password')->getData();
                $password = $encoder->encodePassword($customer, $new_password);
                $customer->setPassword($password);
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
            'wishlist' => $this->wishlist->getFull(),
            'customer' => $this->getUser(),
            'pendingOrders' => $pendingOrders,
            'successOrders' => $successOrders,
            'canceledOrders' => $canceledOrders,
        ]);
    }

}
