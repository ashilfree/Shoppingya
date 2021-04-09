<?php


namespace App\Listener;

use App\Entity\Connected;
use App\Entity\VisitStats;
use App\Repository\VisitStatsRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    public function __construct(EntityManagerInterface $entityManager, VisitStatsRepository $visitStatsRepository, SessionInterface $session)
    {
        $this->entityManager = $entityManager;
        $this->visitStatsRepository = $visitStatsRepository;
        $this->session = $session;
    }

    public function onKernelRequest(RequestEvent $event)
    {
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