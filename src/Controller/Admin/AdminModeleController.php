<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminModeleController extends AbstractController
{
    /**
     * @Route("/admin/modele", name="modele_admin_index")
     */
    public function index(): Response
    {
        return $this->render('admin/admin_modele/index.html.twig', [
            'controller_name' => 'AdminModeleController',
        ]);
    }
}
