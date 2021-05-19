<?php


namespace App\Listener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListener
{

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param LogoutEvent $logoutEvent
     * @return void
     */

    public function onSymfonyComponentSecurityHttpEventLogoutEvent(LogoutEvent $logoutEvent): void
    {
        $locale = $logoutEvent->getRequest()->getSession()->get('locale','en');
        $logoutEvent->setResponse(new RedirectResponse($this->router->generate('home', ['locale' => $locale])));
    }
}