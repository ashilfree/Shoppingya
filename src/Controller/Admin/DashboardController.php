<?php

namespace App\Controller\Admin;

use App\Entity\About;
use App\Entity\Category;
use App\Entity\Customer;
use App\Entity\Governorate;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Slide;
use App\Repository\ConnectedRepository;
use App\Repository\OrderRepository;
use App\Repository\VisitStatsRepository;
use DateTimeZone;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{

    /**
     * @var VisitStatsRepository
     */
    private $visitStatsRepository;
    /**
     * @var ConnectedRepository
     */
    private $connectedRepository;
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    public function __construct(VisitStatsRepository $visitStatsRepository, ConnectedRepository $connectedRepository, OrderRepository $orderRepository)
    {
        $this->visitStatsRepository = $visitStatsRepository;
        $this->connectedRepository = $connectedRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $timestamp_5min = time() - (60 * 5);
        $date = new \DateTime();
        $date->setTimezone(new DateTimeZone('Asia/Kuwait'));
        $date->setTime(0,0);
        $previous_week = (new \DateTime())->modify('-200 day');
        $previous_week->setTimezone(new DateTimeZone('Asia/Kuwait'));
        $previous_week->setTime(0,0);
        $this->connectedRepository->deleteLess5Min($timestamp_5min);
        $connected = $this->connectedRepository->findAll();
        $visitStats = $this->visitStatsRepository->findAll();
//        $visitStatsToday = $this->visitStatsRepository->findToday($date);
        $allOrders = $this->orderRepository->findAll();
        $successOrders = $this->orderRepository->findAllSuccessOrders();
        $lastWeekSuccessOrders = $this->orderRepository->findLastWeekSuccessOrders($previous_week);
        $pendingOrders = $this->orderRepository->findAllPendingOrders();
        $lastWeekPendingOrders = $this->orderRepository->findLastWeekPendingOrders($previous_week);
        $canceledOrders = $this->orderRepository->findAllCanceledOrders();
        $lastWeekCanceledOrders = $this->orderRepository->findLastWeekCanceledOrders($previous_week);
        $lastWeekOrders = $this->orderRepository->lastWeekOrders($previous_week);
        $lastWeekVisits = $this->visitStatsRepository->lastWeekVisits($previous_week);
        $totalVisits = 0;
        foreach ($visitStats as $visit){
            $totalVisits += $visit->getVisitNumber();
        }
        $turnover = 0;
        $today = 0;
        foreach ($successOrders as $order){
            $turnover += ($order->getTotal() + $order->getdeliveryprice());
            if($order->getCreatedAt()->format('Y-m-d') == $date->format('Y-m-d')){
                $today += ($order->getTotal() + $order->getdeliveryprice());
            }
        }
        $visitsByYear = $this->visitStatsRepository->visitsByYear(2021);
        $ordsByYear = $this->orderRepository->ordersByYear(2021);
        $visitorsByYear = [];
        $ordersByYear = [];
        foreach ($visitsByYear as $visit){
            $visitorsByYear[$visit["gBmonth"]] = $visit[1];
        }
        foreach ($ordsByYear as $order){
            $ordersByYear[$order["gBmonth"]] = $order[1];
        }
        for ($i=1;$i <= 12; $i++){
            if(!isset($visitorsByYear[$i]))
                $visitorsByYear[$i] = 0;
            if(!isset($ordersByYear[$i]))
                $ordersByYear[$i] = 0;
        }
        ksort($visitorsByYear);
        ksort($ordersByYear);



        return $this->render('admin/dashboard.html.twig', [
            'connected' => $connected,
            'totalVisits' => $totalVisits,
            //            'visitStatsToday' => $visitStatsToday,
            'allOrders' => $allOrders,
            'successOrders' => $successOrders,
            'lastWeekSuccessOrders' => $lastWeekSuccessOrders,
            'pendingOrders' => $pendingOrders,
            'lastWeekPendingOrders' => $lastWeekPendingOrders,
            'canceledOrders' => $canceledOrders,
            'lastWeekCanceledOrders' => $lastWeekCanceledOrders,
            'visits' => $lastWeekVisits,
            'orders' => $lastWeekOrders,
            'turnover' => $turnover,
            'today'=>$today,
            'visitsByYear' => $visitorsByYear,
            'ordersByYear' => $ordersByYear
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('E Commerce');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
//        yield MenuItem::linkToCrud('User', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Customer', 'fa fa-user', Customer::class);
        yield MenuItem::linkToCrud('Order', 'fa fa-university', Order::class);
        yield MenuItem::linkToCrud('Category', 'fa fa-list', Category::class);
        yield MenuItem::linkToCrud('Slide', 'fa fa-desktop', Slide::class);
        yield MenuItem::linkToCrud('Product', 'fa fa-tags', Product::class);
        yield MenuItem::linkToCrud('Governorate', 'fa fa-university', Governorate::class);
        yield MenuItem::linkToCrud('About Us', 'fa fa-university', About::class);

    }
}
