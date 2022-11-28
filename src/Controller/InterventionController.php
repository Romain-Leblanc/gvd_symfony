<?php

namespace App\Controller;

use App\Entity\Intervention;
use App\Form\AjoutInterventionType;
use App\Repository\InterventionRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InterventionController extends AbstractController
{
    /**
     * @Route("/intervention", name="intervention_index")
     */
    public function index(InterventionRepository $interventionRepository): Response
    {
        $lesInterventions = $interventionRepository->findAll();

        return $this->render('intervention/index.html.twig', [
            'lesInterventions' => $lesInterventions,
        ]);
    }

    /**
     * @Route("/intervention/ajouter", name="intervention_ajouter")
     */
    public function ajouter(InterventionRepository $interventionRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $uneIntervention = new Intervention();
        $form = $this->createForm(AjoutInterventionType::class, $uneIntervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            dd("ajouter", $form->getData());
            $entityManager->persist($uneIntervention);
            $entityManager->flush();

            return $this->redirectToRoute('intervention_index');
        }

        return $this->render('intervention/ajout.html.twig', [
            'errors' => $form->getErrors(true),
            'formAjoutIntervention' => $form->createView()
        ]);
    }

    /**
     * @Route("/intervention/infos", name="intervention_infos")
     */
    public function infos(VehiculeRepository $vehiculeRepository, Request $request)
    {
        $id = (int) $request->request->get('clientID');
        if (!empty($id) && $id !== 0) {
            // Renvoi la liste des véhicules du client pour Ajax
            $liste = $vehiculeRepository->findBy(['fk_client' => $id]);
            return $this->json(['donnees' => $liste]);
        }
        else {
            return $this->json(['donnees' => ""]);
        }
    }
}
