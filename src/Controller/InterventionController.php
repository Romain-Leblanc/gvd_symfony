<?php

namespace App\Controller;

use App\Entity\Intervention;
use App\Form\AjoutInterventionType;
use App\Form\ModificationInterventionType;
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
    public function ajouter(Request $request, EntityManagerInterface $entityManager): Response
    {
        $uneIntervention = new Intervention();
        $form = $this->createForm(AjoutInterventionType::class, $uneIntervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // Redéfinit les valeurs par défaut
            $uneIntervention->setFkFacture(null);
            $uneIntervention->setDateCreation(new \DateTime());

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
     * @Route("/intervention/modifier/{id}", name="intervention_modifier", defaults={"id" = 0})
     */
    public function modifier(int $id, InterventionRepository $interventionRepository, Request $request): Response
    {
        $uneIntervention = $interventionRepository->find($id);
        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($id == 0 || $uneIntervention == null) {
            return $this->redirectToRoute('intervention_index');
        }
        $form = $this->createForm(ModificationInterventionType::class, $uneIntervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            dd($uneIntervention);
            $interventionRepository->updateIntervention($uneIntervention);

            return $this->redirectToRoute('intervention_index');
        }

        return $this->render('intervention/modification.html.twig', [
            'errors' => $form->getErrors(true),
            'formModificationIntervention' => $form->createView()
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
