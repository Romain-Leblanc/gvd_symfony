<?php

namespace App\Controller\Admin;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUtilisateurController extends AbstractController
{
    /**
     * @Route("/admin/utilisateur", name="utilisateur_admin_index")
     */
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        $lesUtilisateurs = $utilisateurRepository->findAll();

        return $this->render('admin/admin_utilisateur/index.html.twig', [
            'lesUtilisateurs' => $lesUtilisateurs,
        ]);
    }
}
