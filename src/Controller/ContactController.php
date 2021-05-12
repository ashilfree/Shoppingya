<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Contact;
use App\Classes\Mailer;
use App\Classes\WishList;
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
     * @var WishList
     */
    private $wishlist;

    /**
     * ContactController constructor.
     * @param Mailer $mailer
     * @param Cart $cart
     * @param WishList $wishlist
     */
    public function __construct(
        Mailer $mailer,
         Cart $cart,
        WishList $wishlist
    )
    {
        $this->mailer = $mailer;
        $this->cart = $cart;
        $this->wishlist = $wishlist;
    }

    /**
     * @Route("/{locale}/contact-us", name="contact.us", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @return Response
     */
    public function index($locale, Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->mailer->sendContactEmail($contact);
            $message = ($locale == "en") ? 'Your Message has been sent' : 'تم ارسال رسالتك';
            $this->addFlash('success', $message);
            return $this->redirectToRoute('contact.us', ['locale' => $locale]);
        }
        $path = ($locale == "en") ? 'contact/index.html.twig' : 'contact/indexAr.html.twig';
        return $this->render($path, [
            'form' => $form->createView(),
            'page' => 'contact.us',
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
        ]);
    }
}
