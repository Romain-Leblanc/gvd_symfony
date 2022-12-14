<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Etat;
use App\Entity\Marque;
use App\Entity\Modele;
use App\Entity\Vehicule;
use App\Form\AjoutVehiculeType;
use App\Form\FiltreTable\FiltreTableVehiculeType;
use App\Form\ModificationVehiculeType;
use App\Repository\InterventionRepository;
use App\Repository\ModeleRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VehiculeController extends AbstractController
{
    /**
     * @Route("/vehicule", name="vehicule_index", methods={"GET", "POST"})
     */
    public function index(VehiculeRepository $vehiculeRepository, Request $request): Response
    {
        $lesVehicules = $vehiculeRepository->findAll();

        $form = $this->createForm(FiltreTableVehiculeType::class, $lesVehicules);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données du formulaire de recherche
            $data = $request->request->get('filtre_table_vehicule');
            $filtre = [];
            // Vérifie si un filtre a été saisi puis définit ses valeurs
            if ($data['id_vehicule'] !== "") { $filtre['id'] = (int) $data['id_vehicule']; }
            if ($data['client'] !== "") { $filtre['fk_client'] = (int) $data['client']; }
            if ($data['vehicule'] !== "") { $filtre['fk_modele'] = (int) $data['vehicule']; }
            if ($data['immatriculation'] !== "") { $filtre['immatriculation'] = (int) $data['immatriculation']; }
            if ($data['etat'] !== "") { $filtre['fk_etat'] = (int) $data['etat']; }
            // Si un filtre a été saisi, on récupère les nouvelles valeurs
            if (isset($filtre)) {
                $lesVehicules = $vehiculeRepository->findBy($filtre);
            }
        }

        return $this->render('vehicule/index.html.twig', [
            'lesVehicules' => $lesVehicules,
            'formFiltreTable' => $form->createView()
        ]);
    }

    /**
     * @Route("/vehicule/ajouter", name="vehicule_ajouter", methods={"GET", "POST"})
     */
    public function ajouter(Request $request, VehiculeRepository $vehiculeRepository, EntityManagerInterface $entityManager): Response
    {
        $unVehicule = new Vehicule();
        $form = $this->createForm(AjoutVehiculeType::class, $unVehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // Si l'immatriculation saisie existe déjà et que l'identifiant du véhiculé modifié est différent
            // de celui du véhicule qui possède l'immatriculation existante, on génère une erreur
            $id = $vehiculeRepository->findOneBy(['immatriculation' => $unVehicule->getImmatriculation()]);
            if(isset($id) && $unVehicule->getId() != $id->getId()) {
                $message = "Cette immatriculation existe déjà pour un autre véhicule.";
                return $this->render('vehicule/ajout.html.twig', [
                    'errors' => $form->addError(new FormError($message))->getErrors(true),
                    'formAjoutVehicule' => $form->createView()
                ]);
            }
            else {
                // Si aucun identifiant de modèle, on génère une erreur
                if(is_null($unVehicule->getFkModele()) || $unVehicule->getFkModele() == "") {
                    $message = "Veuillez sélectionner un modèle de véhicule.";
                    return $this->render('vehicule/ajout.html.twig', [
                        'errors' => $form->addError(new FormError($message))->getErrors(true),
                        'formAjoutVehicule' => $form->createView()
                    ]);
                }
                // Sinon on insère
                else {
                    $entityManager->persist($unVehicule);
                    $entityManager->flush();
                    return $this->redirectToRoute('vehicule_index');
                }
            }
        }

        return $this->render('vehicule/ajout.html.twig', [
            'formAjoutVehicule' => $form->createView(),
            'errors' => $form->getErrors(true),
        ]);
    }

    /**
     * @Route("/vehicule/modifier/{id}", name="vehicule_modifier", defaults={"id" = 0}, methods={"GET", "POST"})
     */
    public function modifier(int $id, VehiculeRepository $vehiculeRepository, InterventionRepository $interventionRepository, Request $request): Response
    {
        $unVehicule = $vehiculeRepository->find($id);
        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($id == 0 || $unVehicule == null) {
            $this->addFlash('vehicule', 'Ce véhicule n\'existe pas.');
            return $this->redirectToRoute('vehicule_index');
        }
        // Si le véhicule est déjà dans une intervention, on ne peut pas modifier à quel client appartient ce véhicule, ni la marque et le modèle.
        $options = $interventionRepository->findBy(['fk_vehicule' => $unVehicule->getId()]);
        $form = $this->createForm(ModificationVehiculeType::class, $unVehicule, ["intervention" => $options]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si l'immatriculation saisie existe déjà et que l'identifiant du véhiculé modifié est différent
            // de celui du véhicule qui possède l'immatriculation existante, on génère une erreur
            $id = $vehiculeRepository->findOneBy(['immatriculation' => $unVehicule->getImmatriculation()]);
            if(isset($id) && $unVehicule->getId() != $id->getId()) {
                $message = "Cette immatriculation existe déjà pour un autre véhicule.";
                return $this->render('vehicule/modification.html.twig', [
                    'errors' => $form->addError(new FormError($message))->getErrors(true),
                    'formModificationVehicule' => $form->createView()
                ]);
            }
            // Sinon on met à jour les données
            else {
                $vehiculeRepository->updateVehicule($unVehicule);
                return $this->redirectToRoute('vehicule_index');
            }
        }

        return $this->render('vehicule/modification.html.twig', [
            'errors' => $form->getErrors(true),
            'formModificationVehicule' => $form->createView()
        ]);
    }

    /**
     * @Route("/vehicule/infos", name="vehicule_infos", methods={"POST"})
     */
    public function infos(ModeleRepository $modeleRepository, Request $request)
    {
        // Récupère l'identifiant pour la requête
        $id = (int) $request->request->get('marqueID');
        if (!empty($id) && $id !== 0) {
            // Renvoi la liste des modèles de la marque de voiture pour Ajax au format JSON
            $liste = $modeleRepository->findBy(['fk_marque' => $id]);
            return $this->json(['donnees' => $liste]);
        }
        else {
            $this->addFlash('vehicule', 'Cet accès est restreint.');
            return $this->redirectToRoute('vehicule_index');
        }
    }
}
