<?php

namespace App\Classes;

use App\Entity\Customer;
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
        $body = $this->twig->render('contact/confirmation.html.twig',
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
}