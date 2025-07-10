<?php

namespace App\Controller\Admin;

use App\Entity\Center;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;


#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
   public function index(): Response
{
    return $this->render('admin/dashboard.html.twig');
}

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Youth Center App');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
         yield MenuItem::linkToCrud('Center', 'fas fa-list', Center::class);
         yield MenuItem::linkToCrud('User', 'fas fa-list', User::class);
         yield MenuItem::linkToRoute('Logout', 'fa fa-sign-out', 'app_logout');
         
    }
}
