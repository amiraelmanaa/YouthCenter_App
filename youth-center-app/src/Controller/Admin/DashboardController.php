<?php

namespace App\Controller\Admin;

use App\Entity\Center;
use App\Entity\User;
use App\Entity\Activities;
use App\Entity\CenterManager;
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
    /** @var AdminUrlGenerator $adminUrlGenerator */
    $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

    $userUrl = $adminUrlGenerator->setController(UserCrudController::class)->generateUrl();
    $activitiesUrl = $adminUrlGenerator->setController(ActivitiesCrudController::class)->generateUrl();
    $centerUrl = $adminUrlGenerator->setController(CenterCrudController::class)->generateUrl();

    return $this->render('admin/dashboard.html.twig', [
        'user_crud_url' => $userUrl,
        'activities_crud_url' => $activitiesUrl,
        'center_crud_url' => $centerUrl,
    ]);
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
         yield MenuItem::linkToCrud('Activities', 'fas fa-list', Activities::class);
         yield MenuItem::linkToCrud('Centers managers', 'fas fa-list', CenterManager::class);
         yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');
         
    }
}
