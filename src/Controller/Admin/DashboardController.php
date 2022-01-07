<?php

namespace App\Controller\Admin;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;


class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(AdminUrlGenerator::class);

        return $this->redirect($routeBuilder->setController(BlogPostCrudController::class)->generateUrl());
    }

    public function configureMenuItems() :iterable
    {
        yield MenuItem::section('Important');
        yield MenuItem::linkToCrud('Posts', 'fa  fa-file-pdf', BlogPost::class);
        yield MenuItem::linkToCrud('Comments', 'fa  fa-file-pdf', Comment::class);
        yield MenuItem::linkToCrud('User', 'fa  fa-file-pdf', User::class);
    }

}
