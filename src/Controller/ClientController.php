<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\AjoutClientType;
use App\Form\FiltreTable\FiltreTableClientType;
use App\Form\ModificationClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    /**
     * @Route("/client", name="client_index", methods={"GET", "POST"})
     */
    public function index(ClientRepository $clientRepository, Request $request): Response
    {
        $lesClients = $clientRepository->findAll();

        $form = $this->createForm(FiltreTableClientType::class, $lesClients);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données du formulaire de recherche
            $data = $request->request->get('filtre_table_client');
            // Si au moins un filtre a été saisi, on récupère les résultats
            if ($data['id_client'] !== ""
                || $data['client'] !== ""
                || $data['coordonnees'] !== ""
                || $data['adresse_complete'] !== "") {
                $lesClients = $clientRepository->filtreTableClient((array) $data);
            }
        }

        return $this->render('client/index.html.twig', [
            'lesClients' => $lesClients,
            'formFiltreTable' => $form->createView()
        ]);
    }

    /**
     * @Route("/client/ajouter", name="client_ajouter", methods={"GET", "POST"})
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

    /**
     * @Route("/client/modifier/{id}", name="client_modifier", defaults={"id" = 0}, methods={"GET", "POST"})
     */
    public function modifier(int $id, ClientRepository $clientRepository, Request $request): Response
    {
        $unClient = $clientRepository->find($id);

        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($id == 0 || $unClient == null) {
            $this->addFlash('client', 'Ce client n\'existe pas.');
            return $this->redirectToRoute('client_index');
        }
        $form = $this->createForm(ModificationClientType::class, $unClient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $clientRepository->updateClient($unClient);

            return $this->redirectToRoute('client_index');
        }

        return $this->render('client/modification.html.twig', [
            'errors' => $form->getErrors(true),
            'formModificationClient' => $form->createView()
        ]);
    }
}
