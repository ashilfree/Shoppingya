<?php


namespace App\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    private $defaultLocale;
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * LocaleSubscriber constructor.
     * @param string $defaultLocale
     * @param SessionInterface $session
     */
    public function __construct(string $defaultLocale = 'en', SessionInterface $session)
    {
        $this->defaultLocale = $defaultLocale;
        $this->session = $session;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }
        $locale = $request->query->get('locale') ?? $request->attributes->get('locale');
        // try to see if the locale has been set as a _locale routing parameter
        if ($locale) {
            $request->getSession()->set('locale', $locale);
            $this->session->set('locale', $locale);
        } else {
            // if no explicit locale has been set on this request, use one from the session
            $request->setLocale($request->getSession()->get('locale', $this->defaultLocale));
        }
       // dd($request->getSession()->get('locale'));
    }

    public static function getSubscribedEvents()
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}