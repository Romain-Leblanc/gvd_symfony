<?php

namespace App\Controller;

use App\Entity\Intervention;
use App\Form\AjoutInterventionType;
use App\Form\ModificationInterventionType;
use App\Repository\InterventionRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
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
//        $lesInterventions = $interventionRepository->findAll();
        $lesInterventions = $interventionRepository->findBy([], ['id' => 'DESC']);

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
            $request->getSession()->getFlashBag()->add('intervention', 'Cette intervention n\'existe pas.');
            return $this->redirectToRoute('intervention_index');
        }

        $form = $this->createForm(ModificationInterventionType::class, $uneIntervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si l'intervention s'apprête à être terminée et que le montant HT est à zéro, on génère une erreur
            if($uneIntervention->getFkEtat()->getEtat() == "Terminé" && $uneIntervention->getMontantHt() == 0) {
                $message = "Le statut de l'intervention est défini sur 'terminé' mais le montant HT est à zéro.";
                return $this->render('intervention/modification.html.twig', [
                    'errors' => $form->addError(new FormError($message))->getErrors(true),
                    'formModificationIntervention' => $form->createView()
                ]);
            }
            // Sinon on met à jour les données
            else {
                $interventionRepository->updateIntervention($uneIntervention);
                return $this->redirectToRoute('intervention_index');
            }
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
        // Si la requête est bien en POST
        if($request->isMethod(Request::METHOD_POST)) {
            if (!empty($id) && $id !== 0) {
                // Renvoi la liste des véhicules du client pour Ajax
                $liste = $vehiculeRepository->findBy(['fk_client' => $id]);
                return $this->json(['donnees' => $liste]);
            }
            else {
                $request->getSession()->getFlashBag()->add('intervention', 'Cet accès est restreint.');
                return $this->redirectToRoute('intervention_index');
            }
        }
        else {
            $request->getSession()->getFlashBag()->add('intervention', 'Cet accès est restreint.');
            return $this->redirectToRoute('intervention_index');
        }
    }
}
