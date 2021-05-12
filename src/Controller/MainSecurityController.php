<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Mailer;
use App\Classes\WishList;
use App\Entity\Customer;
use App\Form\CustomerRegisterType;
use App\Security\CustomerConfirmationService;
use App\Security\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MainSecurityController extends AbstractController
{
    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Cart
     */
    private $cart;
    /**
     * @var WishList
     */
    private $wishlist;

    /**
     * MainSecurityController constructor.
     * @param AuthenticationUtils $authenticationUtils
     * @param Cart $cart
     * @param WishList $wishlist
     * @param Mailer $mailer
     */
    public function __construct(
        AuthenticationUtils $authenticationUtils,
        Cart $cart,
        WishList $wishlist,
        Mailer $mailer
    )
    {
        $this->authenticationUtils = $authenticationUtils;

        $this->mailer = $mailer;

        $this->cart = $cart;
        $this->wishlist = $wishlist;
    }


    /**
     * @Route("/{locale}/login", name="login", defaults={"locale"="en"})
     * @param $locale
     * @return Response
     */
    // TODO: Use Facebook and Google to Login
    public function login($locale): Response
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();
        $path = ($locale == "en") ? 'authentication/login.html.twig' : 'authentication/loginAr.html.twig';
            return $this->render($path, [
                'page' => 'login',
                'last_username' => $lastUsername,
                'error' => $error,
                'cart' => $this->cart->getFull($this->cart->get()),
                'wishlist' => $this->wishlist->getFull()
            ]);
    }

    /**
     * @Route("/{locale}/register", name="register", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenGenerator $tokenGenerator
     * @return Response
     */
    // TODO: Use Facebook and Google to Register
    public function register($locale, Request $request, UserPasswordEncoderInterface $passwordEncoder, TokenGenerator $tokenGenerator): Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerRegisterType::class, $customer);
        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($customer, $customer->getPassword());
            $customer->setPassword($password);
            $customer->setConfirmationToken($tokenGenerator->getRandomSecureToken());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customer);
            $entityManager->flush();
            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user
//            $this->addFlash('success', 'Your account has been registered.');
            //send mail to customer
            $this->mailer->sendConfirmationEmail($customer);
            //return $this->redirectToRoute('login');
            $path = ($locale == "en") ? 'authentication/check-email-register.html.twig' : 'authentication/check-email-registerAr.html.twig';
            return $this->render($path, [
                'cart' => $this->cart->getFull($this->cart->get()),
                'wishlist' => $this->wishlist->getFull(),
                'page'=> 'register',
            ]);
        }
        $path = ($locale == "en") ? 'authentication/register.html.twig' : 'authentication/registerAr.html.twig';
        return $this->render($path, [
            'page'=> 'register',
            'form'=>$form->createView(),
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
        ]);
    }

    /**
     * @param string $token
     * @param CustomerConfirmationService $customerConfirmationService
     * @return RedirectResponse
     * @Route("/confirm-customer/{token}", name="default.confirm.token")
     */
    public function confirmCustomer(
        string $token,
        CustomerConfirmationService $customerConfirmationService
    ): RedirectResponse
    {
        $customerConfirmationService->confirmCustomer($token);
        return $this->redirectToRoute('confirmation');
    }

    /**
     * @Route("/confirmation", name="confirmation")
     * @return Response
     */
    public function confirmation(): Response
    {
        return $this->render('authentication/confirmation.html.twig', [
            'page' => 'confirmation',
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
        ]);
    }
}