<?php

namespace App\Classes;

use App\Entity\Customer;
use App\Entity\Order;
use Twig\Environment;

class Mailer{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $twig;


    /**
     * Mailer constructor.
     * @param \Swift_Mailer $mailer
     * @param Environment $twig
     */
    public function __construct(
        \Swift_Mailer $mailer,
        Environment $twig
)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendConfirmationEmail(Customer $customer)
    {
        $body = $this->twig->render('authentication/email.mjml.twig',
            [
                'customer' => $customer
            ]
        );
        $message = (new \Swift_Message('Please confirm your account'))
            ->setFrom('noreply@agence.fr')
            ->setTo($customer->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }

    public function sendResetPasswordEmail($customer, $resetToken, $tokenLifetime)
    {
        $body = $this->twig->render('authentication/email.html.twig', [
                'resetToken' => $resetToken,
                'tokenLifetime' => $tokenLifetime,
            ]
        );
        $message = (new \Swift_Message('Your password reset request'))
            ->setFrom('mohammed@genesistech-dz.com')
            ->setTo($customer->getEmail())
            ->setReplyTo($customer->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }

    public function sendContactEmail(Contact $contact)
    {
        $body = $this->twig->render('contact/contact.html.twig', [
                'contact' => $contact
            ]
        );
        $message = (new \Swift_Message('Agency : ' . $contact->getSubject()))
            ->setFrom('noreply@agence.fr')
            ->setTo($contact->getEmail())
            ->setReplyTo($contact->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }

    public function sendSuccessOrderEmail(Order $order)
    {
        $body = $this->twig->render('order/success.html.twig',
            [
                'order' => $order
            ]
        );
        $message = (new \Swift_Message('Please confirm your account'))
            ->setFrom('noreply@agence.fr')
            ->setTo($order->getShippingEmail())
            ->setReplyTo($order->getShippingEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }

    public function sendFailureOrderEmail(Order $order)
    {
        $body = $this->twig->render('order/failure.html.twig',
            [
                'order' => $order
            ]
        );
        $message = (new \Swift_Message('Please confirm your account'))
            ->setFrom('noreply@agence.fr')
            ->setTo($order->getShippingEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}