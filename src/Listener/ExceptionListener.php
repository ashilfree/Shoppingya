<?php


namespace App\Listener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExceptionListener
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(UrlGeneratorInterface $router, SessionInterface $session, EventDispatcherInterface $dispatcher)
    {
        $this->router = $router;
        $this->session = $session;
        $this->dispatcher = $dispatcher;
    }
    public function onKernelException(ExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();
        if($exception->getMessage()== 'The argument of the "EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField::setCurrency()" method must be a valid currency code according to ICU data ("KWD" given).'){
            $event->setResponse(new RedirectResponse($this->router->generate('logout_admin')));
        }
        // Customize your response object to display the exception details
//        $response = new Response();
//        $response->setContent($message);
//
//        // HttpExceptionInterface is a special type of exception that
//        // holds status code and header details
//        if ($exception instanceof HttpExceptionInterface) {
//            $response->setStatusCode($exception->getStatusCode());
//            $response->headers->replace($exception->getHeaders());
//        } else {
//            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
//        }
//
//        // sends the modified response object to the event
//        $event->setResponse($response);

    }
}