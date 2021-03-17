<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Contact;
use App\Classes\Mailer;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Cart
     */
    private $cart;

    /**
     * ContactController constructor.
     * @param Mailer $mailer
     * @param Cart $cart
     */
    public function __construct(
        Mailer $mailer,
         Cart $cart
    )
    {
        $this->mailer = $mailer;
        $this->cart = $cart;
    }

    /**
     * @Route("/contact-us", name="contact.us")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->mailer->sendContactEmail($contact);
            $this->addFlash('success', 'Your Message has been sent');
            return $this->redirectToRoute('contact.us');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
            'page' => 'contact.us',
            'cart' => $this->cart->getFull($this->cart->get()),
        ]);
    }
}
