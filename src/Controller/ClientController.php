<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\AjoutClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    /**
     * @Route("/client", name="client_index")
     */
    public function index(ClientRepository $clientRepository): Response
    {
        $lesClients = $clientRepository->findAll();

        return $this->render('client/index.html.twig', [
            'lesClients' => $lesClients
        ]);
    }

    /**
     * @Route("/client/ajouter", name="client_ajouter")
     */
    public function ajouter(Request $request, EntityManagerInterface $entityManager): Response
    {
        $unClient = new Client();
        $form = $this->createForm(AjoutClientType::class, $unClient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($unClient);
            $entityManager->flush();

            return $this->redirectToRoute('client_index');
        }

        return $this->render('client/ajout.html.twig', [
            'errors' => $form->getErrors(true),
            'formAjoutClient' => $form->createView()
        ]);
    }
}
