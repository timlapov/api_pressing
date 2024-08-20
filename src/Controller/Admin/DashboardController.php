<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\City;
use App\Entity\Client;
use App\Entity\Country;
use App\Entity\Employee;
use App\Entity\Gender;
use App\Entity\Item;
use App\Entity\Order;
use App\Entity\OrderStatus;
use App\Entity\Service;
use App\Entity\ServiceCoefficients;
use App\Entity\Subcategory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
//        return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
         $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
         return $this->redirect($adminUrlGenerator->setController(OrderCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    #[Route('/admin/login', name: 'admin_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('@EasyAdmin/page/login.html.twig', [
            // parameters usually defined in Symfony login forms
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),

            // OPTIONAL parameters to customize the login form:
            'translation_domain' => 'admin',
            'page_title' => 'Se connecter au panneau de contrôle de l\'administrateur',
            'csrf_token_intention' => 'authenticate',
            'username_label' => 'Votre email',
            'password_label' => 'Votre mot de passe',
            'sign_in_label' => 'S\'identifier',
            'username_parameter' => 'email',
            'password_parameter' => 'password',
        ]);
    }

    #[Route('/admin/logout', name: 'admin_logout', methods: ['GET'])]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin Panel – Propre-Propre');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Orders', 'fas fa-list', Order::class);
        yield MenuItem::linkToCrud('Coefficients', 'fas fa-list', ServiceCoefficients::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('Cities', 'fas fa-list', City::class);
        yield MenuItem::linkToCrud('Countries', 'fas fa-list', Country::class);
        yield MenuItem::linkToCrud('Clients', 'fas fa-list', Client::class);
        yield MenuItem::linkToCrud('Employees', 'fas fa-list', Employee::class);
        yield MenuItem::linkToCrud('Genders', 'fas fa-list', Gender::class);
        yield MenuItem::linkToCrud('Items', 'fas fa-list', Item::class);
        yield MenuItem::linkToCrud('Order Statuses', 'fas fa-list', OrderStatus::class);
        yield MenuItem::linkToCrud('Services', 'fas fa-list', Service::class);
        yield MenuItem::linkToCrud('Subcategories', 'fas fa-list', Subcategory::class);
    }
}
