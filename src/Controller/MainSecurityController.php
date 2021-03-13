<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Mailer;
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
     * MainSecurityController constructor.
     * @param AuthenticationUtils $authenticationUtils
     * @param Cart $cart
     * @param Mailer $mailer
     */
    public function __construct(
        AuthenticationUtils $authenticationUtils,
        Cart $cart,
        Mailer $mailer
    )
    {
        $this->authenticationUtils = $authenticationUtils;

        $this->mailer = $mailer;

        $this->cart = $cart;
    }


    /**
     * @Route("/login", name="login")
     * @return Response
     */
    // TODO: Use Facebook and Google to Login
    public function login()
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('authentication/login.html.twig', [
            'page' => 'login',
            'last_username' => $lastUsername,
            'error' => $error,
            'cart' => $this->cart->getFull($this->cart->get())
        ]);
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenGenerator $tokenGenerator
     * @return Response
     */
    // TODO: Use Facebook and Google to Register
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, TokenGenerator $tokenGenerator): Response
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
            $this->addFlash('success', 'Votre compte à bien été enregistré.');
            //send mail to customer
            $this->mailer->sendConfirmationEmail($customer);
            //return $this->redirectToRoute('login');
            return $this->redirectToRoute('register');
        }

        return $this->render('authentication/register.html.twig', [
            'page'=> 'register',
            'form'=>$form->createView(),
            'cart' => $this->cart->getFull($this->cart->get())
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
    )
    {
        $customerConfirmationService->confirmCustomer($token);
        return $this->redirectToRoute('confirmation');
    }

    /**
     * @Route("/confirmation", name="confirmation")
     * @return Response
     */
    public function confirmation()
    {
        return $this->render('authentication/confirmation.html.twig', [
            'page' => 'confirmation'
        ]);
    }
}