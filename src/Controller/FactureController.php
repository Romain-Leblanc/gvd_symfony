<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Form\AjoutFactureType;
use App\Form\ModificationFactureType;
use App\Repository\EtatRepository;
use App\Repository\FactureRepository;
use App\Repository\InterventionRepository;
use App\Repository\TVARepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FactureController extends AbstractController
{
    /**
     * @Route("/facture", name="facture_index")
     */
    public function index(FactureRepository  $factureRepository): Response
    {
        $lesFactures = $factureRepository->findAll();

        return $this->render('facture/index.html.twig', [
            'lesFactures' => $lesFactures
        ]);
    }

    /**
     * @Route("/facture/ajouter", name="facture_ajouter")
     */
    public function ajouter(InterventionRepository $interventionRepository, TVARepository $TVARepository, EtatRepository $etatRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Retourne la liste des interventions qui sont terminées
        $listeInterventions = $interventionRepository->findBy(['fk_etat' => $etatRepository->findOneBy(['etat' => 'Terminé'])->getId()]);

        // Si aucune intervention est terminée (et donc n'a pas besoin d'être facturé), alors on renvoie un message puis une redirection
        if(empty($listeInterventions)){
            $request->getSession()->getFlashBag()->add('facture', 'Aucun intervention à facturée.');
            return $this->redirectToRoute("intervention_index");
        }

        // Retourne le taux de TVA égal à 20
        $tauxTVA = $TVARepository->findOneBy(["taux" => 20])->getTaux();

        // Création de l'objet Facture(), génération du formulaire d'ajout d'une Facture avec l'objet Facture et manipulation des données de l'objet Request
        $uneFacture = new Facture();
        $form = $this->createForm(AjoutFactureType::class, $uneFacture);
        $form->handleRequest($request);

        // Si le formulaire a bien été soumis et est validé
        if ($form->isSubmitted() && $form->isValid()){
            // On persiste l'objet Facture dans l'entité Facture
            $entityManager->persist($uneFacture);
            $entityManager->flush();

            // Equivalent de la fonction lastInsertId() qui permet de récupérer le dernier identifiant insérée dans la table facture
            $idFacture = $uneFacture->getId();

            // Récupère la liste des interventions terminées du client qui ne sont pas facturées
            $liste = $interventionRepository->findBy(['fk_client' => $uneFacture->getFKClient()->getId(), 'fk_facture' => null, 'fk_etat' => $etatRepository->findOneBy(['etat' => 'Terminé'])->getId()]);

            // Boucle sur chaque intervention pour récupérer l'identifiant de l'intervention du client à facturé puis concatène ces identifiants dans un tableau
            $tabIdInterventions = [];
            foreach ($liste as $value){
                array_push($tabIdInterventions, $value->getId());
            }

            // Met à jour l'etat des interventions à 'Facturé' et associe le dernier n° facture aux identifiants du tableau ci-dessus
            $interventionRepository->updateInterventionByEtatAndNumFacture($tabIdInterventions, $etatRepository->findOneBy(['etat' => 'Facturé'])->getId(), $idFacture);

            // Redirection de la page vers le tableau principal
            return $this->redirectToRoute('facture_index');
        }

        return $this->render('facture/ajout.html.twig', [
            'errors' => $form->getErrors(true),
            'formAjoutFacture' => $form->createView(),
            'listeInterventions' => $listeInterventions,
            'tauxTVA' => $tauxTVA,
        ]);
    }

    /**
     * @Route("/facture/modifier/{id}", name="facture_modifier", defaults={"id" = 0})
     */
    public function modifier(FactureRepository $factureRepository, InterventionRepository $interventionRepository, $id, Request $request): Response
    {
        $uneFacture = $factureRepository->find($id);
        $listeInterventions = $interventionRepository->findBy(['fk_client' => $uneFacture->getFKClient()->getId(), 'fk_facture' => $uneFacture->getId()]);

        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($id == 0 || $uneFacture == null) {
            $request->getSession()->getFlashBag()->add('facture', 'Cette facture n\'existe pas.');
            return $this->redirectToRoute('facture_index');
        }

        $form = $this->createForm(ModificationFactureType::class, $uneFacture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $factureRepository->updateFacture($uneFacture);

            return $this->redirectToRoute('facture_index');
        }

        return $this->render('facture/modification.html.twig', [
            'errors' => $form->getErrors(true),
            'formModificationFacture' => $form->createView(),
            'listeInterventions' => $listeInterventions
        ]);
    }

    /**
     * @Route("/facture/infos", name="facture_infos")
     */
    public function infos(InterventionRepository $interventionRepository, EtatRepository $etatRepository, Request $request)
    {
        $id = (int) $request->request->get('clientID');
        // Si la requête est bien en POST
        if($request->isMethod(Request::METHOD_POST)) {
            if (!empty($id) && $id !== 0) {
                // Renvoi la liste des interventions non facturés des véhicules du client
                $liste = $interventionRepository->findBy(['fk_client' => $id, 'fk_facture' => null, 'fk_etat' => $etatRepository->findOneBy(['etat' => 'Terminé'])->getId()]);
                return $this->json(['donnees' => $liste]);
            }
            else {
                return $this->json(['donnees' => ""]);
            }
        }
        else {
            return $this->json(['donnees' => ""]);
        }
    }
}
