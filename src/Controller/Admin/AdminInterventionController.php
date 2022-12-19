<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminInterventionController extends AbstractController
{
    /**
     * @Route("/admin/intervention", name="intervention_admin_index")
     */
    public function index(): Response
    {
        return $this->render('admin/admin_intervention/index.html.twig', [
            'controller_name' => 'AdminInterventionController',
        ]);
    }
}
