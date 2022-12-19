<?php

namespace App\Controller\Admin;

use App\Repository\MarqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminMarqueController extends AbstractController
{
    /**
     * @Route("/admin/marque", name="marque_admin_index")
     */
    public function index(MarqueRepository $marqueRepository): Response
    {
        $lesMarques = $marqueRepository->findAllWithNombreModele();

        return $this->render('admin/admin_marque/index.html.twig', [
            'lesMarques' => $lesMarques,
        ]);
    }
}
