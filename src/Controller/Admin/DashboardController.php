<?php

namespace App\Controller\Admin;

use App\Entity\Catalog;
use App\Entity\Category;
use App\Entity\Customer;
use App\Entity\Governorate;
use App\Entity\Product;
use App\Entity\Slide;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('E Commerce');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('User', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Customer', 'fa fa-user', Customer::class);
        yield MenuItem::linkToCrud('Category', 'fa fa-list', Category::class);
        yield MenuItem::linkToCrud('Slide', 'fa fa-desktop', Slide::class);
        yield MenuItem::linkToCrud('Product', 'fa fa-tags', Product::class);
        yield MenuItem::linkToCrud('Catalog', 'fa fa-catalog', Catalog::class);
        yield MenuItem::linkToCrud('Governorate', 'fa fa-university', Governorate::class);
    }
}
