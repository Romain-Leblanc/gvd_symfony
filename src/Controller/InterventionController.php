<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Intervention;
use App\Entity\Marque;
use App\Entity\Modele;
use App\Entity\Vehicule;
use App\Form\AjoutInterventionType;
use App\Form\FiltreTable\FiltreTableInterventionType;
use App\Form\ModificationInterventionType;
use App\Repository\EtatRepository;
use App\Repository\InterventionRepository;
use App\Repository\VehiculeRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InterventionController extends AbstractController
{
    /**
     * @Route("/intervention", name="intervention_index")
     */
    public function index(InterventionRepository $interventionRepository, Request $request): Response
    {
        $lesInterventions = $interventionRepository->findBy([], ['id' => 'DESC']);

        $form = $this->createForm(FiltreTableInterventionType::class, $lesInterventions);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données du formulaire de recherche
            $data = $request->request->get('filtre_table_intervention');
            $filtre = [];
            // Vérifie si un filtre a été saisi puis définit ses valeurs
            if ($data['id_intervention'] !== "") { $filtre['id'] = (int) $data['id_intervention']; }
            if ($data['date_intervention'] !== "") { $filtre['date_intervention'] = new DateTime($data['date_intervention']); }
            if ($data['vehicule'] !== "") { $filtre['fk_vehicule'] = (int) $data['vehicule']; }
            if ($data['client'] !== "") { $filtre['fk_client'] = (int) $data['client']; }
            if ($data['montant_ht'] !== "") { $filtre['montant_ht'] = $data['montant_ht']; }
            // Si un filtre a été saisi, on récupère les nouvelles valeurs
            if (isset($filtre)) {
                $lesInterventions = $interventionRepository->findBy($filtre, ['id' => 'DESC']);
            }
        }

        return $this->render('intervention/index.html.twig', [
            'lesInterventions' => $lesInterventions,
            'formFiltreTable' => $form->createView()
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
                $message = "L'état de l'intervention est défini sur 'Terminé' mais le montant HT est à zéro.";
                return $this->render('intervention/modification.html.twig', [
                    'errors' => $form->addError(new FormError($message))->getErrors(true),
                    'formModificationIntervention' => $form->createView()
                ]);
            }
            // Sinon si le type d'état de l'intervention concerne ceux des véhicules, on génère une erreur
            elseif ($uneIntervention->getFkEtat()->getType() == "vehicule") {
                $message = "L'état de l'intervention doit être concernés ceux pour les interventions.";
                return $this->render('intervention/modification.html.twig', [
                    'errors' => $form->addError(new FormError($message))->getErrors(true),
                    'formModificationIntervention' => $form->createView()
                ]);
            }
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
    public function infos(VehiculeRepository $vehiculeRepository, EtatRepository $etatRepository, Request $request)
    {
        $id = (int) $request->request->get('clientID');
        // Si la requête est bien en POST
        if($request->isMethod(Request::METHOD_POST)) {
            if (!empty($id) && $id !== 0) {
                // Renvoi la liste des véhicules fonctionnel du client
                $liste = $vehiculeRepository->findBy(['fk_client' => $id, 'fk_etat' => $etatRepository->findOneBy(['etat' => 'Fonctionnel', 'type' => 'vehicule'])]);
                return $this->json(['donnees' => $liste]);
            }
        }
        else {
            $request->getSession()->getFlashBag()->add('intervention', 'Cet accès est restreint.');
            return $this->redirectToRoute('intervention_index');
        }
    }
}
