<?php

// src/EventListener/LoginListener.php

namespace App\Listener;

use App\Classes\Cart;
use App\Entity\Customer;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    private $em;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var Cart
     */
    private $cart;
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher, Security $security, Cart $cart, UrlGeneratorInterface $router)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->security = $security;
        $this->cart = $cart;
        $this->router = $router;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $this->dispatcher->addListener(KernelEvents::RESPONSE, array($this, 'onKernelResponse'));
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $locale = $event->getRequest()->getSession()->get('locale');
        $user = $this->security->getUser();
        if($user instanceof Customer) {
            $order = $this->em->getRepository(Order::class)->findOneBy([
                'customer' => $user,
                'stripeSessionId' => null
            ]);
            if($order) {
                $this->cart->createCart2Order($order);
                $event->setResponse(new RedirectResponse($this->router->generate('order', ['locale' => $locale,'from' => false])));
            }else{
                $event->setResponse(new RedirectResponse($this->router->generate('home', ['locale' => $locale])));
            }

        }
    }
}