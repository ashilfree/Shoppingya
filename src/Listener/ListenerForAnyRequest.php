<?php


namespace App\Listener;

use App\Classes\Cart;
use App\Classes\Transaction;
use App\Entity\Connected;
use App\Entity\Order;
use App\Entity\VisitStats;
use App\Repository\VisitStatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class ListenerForAnyRequest
{


    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var VisitStatsRepository
     */
    private $visitStatsRepository;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var Transaction
     */
    private $transaction;
    /**
     * @var Cart
     */
    private $cart;

    public function __construct(EntityManagerInterface $entityManager, VisitStatsRepository $visitStatsRepository, SessionInterface $session, Transaction $transaction, Cart $cart)
    {
        $this->entityManager = $entityManager;
        $this->visitStatsRepository = $visitStatsRepository;
        $this->session = $session;
        $this->transaction = $transaction;
        $this->cart = $cart;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if($event->getRequest()->headers->get('referer')){
            $route = $event->getRequest()->attributes->get('_route');
            if($route !== '_wdt') {
                $refererPathInfo = Request::create($event->getRequest()->headers->get('referer'))->getPathInfo();
                if (($refererPathInfo == '/en/order/recap' && $route !== 'order' && $route !== 'my.fatoorah.create.session') || ($refererPathInfo == '/en/order' && $route !== 'order.recap' && $route != 'order')) {
                    if ($this->session->get('orderId')) {
                        $oldOrder = $this->entityManager->getRepository(Order::class)->find($this->session->get('orderId'));
                        if($this->transaction->check($oldOrder, 'order_canceled2'))
                            $this->transaction->applyWorkFlow($oldOrder, 'order_canceled2');
                        if($this->transaction->check($oldOrder, 'order_canceled'))
                            $this->transaction->applyWorkFlow($oldOrder, 'order_canceled');
                        $this->cart->increaseStock();
                    }
                    $this->session->clear();
                }
            }
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        $date = date('Y-m-d');
        $page = substr($_SERVER['PHP_SELF'], 1);
        $visitor = $this->visitStatsRepository->findBy(['ip' => $ip], array('id'=>'DESC'),1,0);

        if(!$this->session->get('online', false)) {
            if ($visitor != null) {
                if(!($visitor[0]->getVisitAt()->format('Y-m-d') == $date)){
                    $visitor = new VisitStats();
                    $visitor->setIp($ip);
                    $visitor->setVisitAt(new \DateTime());
                    $visitor->setVisitNumber(1);
                    $this->entityManager->persist($visitor);
                }else{
                    $count = $visitor[0]->getVisitNumber();
                    $count++;
                    $visitor[0]->setVisitNumber($count);
                }
            } else {
                $visitor = new VisitStats();
                $visitor->setIp($ip);
                $visitor->setVisitAt(new \DateTime());
                $visitor->setVisitNumber(1);
                $this->entityManager->persist($visitor);
            }
            $connected = new Connected();
            $connected->setIp($ip);
            $connected->setTimestamp(time());
            $connected->setPage($page);
            $this->entityManager->persist($connected);
            $this->entityManager->flush();
            $this->session->set('online', true);
        }
    }
}