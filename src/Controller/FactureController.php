<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Form\AjoutFactureType;
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
    public function ajouter(FactureRepository $factureRepository, InterventionRepository $interventionRepository, TVARepository $TVARepository, EtatRepository $etatRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Retourne la liste des interventions qui sont terminées
        $listeInterventions = $interventionRepository->findBy(['fk_etat' => $etatRepository->findOneBy(['etat' => 'Terminé'])->getId()]);

        // Si aucune intervention est terminée (et donc n'a pas besoin d'être facturé), alors on renvoie un message puis une redirection
        if(empty($listeInterventions)){
            echo '<script type="application/javascript">alert("Aucun intervention à facturée");</script>';
            $this->redirectToRoute("facture_index");
        }

        // Retourne le taux de TVA égal à 20
        $tauxTVA = $TVARepository->findOneBy(["taux" => 20])->getTaux();

        // Si aucune intervention est terminée (et donc n'a pas besoin d'être facturé), alors on renvoie un message puis une redirection
/*        if(empty($tauxTVA)){
            echo '<script type="application/javascript">alert("Aucun taux de TVA égal à 20% est présent dans la base de données.");</script>';
            $this->redirectToRoute("facture_index");
        }*/

        // Création de l'objet Facture(), génération du formulaire d'ajout d'une Facture avec l'objet Facture et manipulation des données de l'objet Request
        $uneFacture = new Facture();
        $form = $this->createForm(AjoutFactureType::class, $uneFacture);
        $form->handleRequest($request);
//        dd($uneFacture);

        // Si le formulaire a bien été soumis et est validé
        if ($form->isSubmitted() && $form->isValid()){
            // On persiste l'objet Facture dans l'entité Facture
            $entityManager->persist($uneFacture);
            $entityManager->flush();

            // Equivalent de la fonction lastInsertId() qui permet de récupérer le dernier identifiant insérée dans la table facture
            $idFacture = $uneFacture->getId();

            // Récupère la liste des interventions terminées du client qui ne sont pas facturées
            $liste = $interventionRepository->findBy(['FK_Client' => $uneFacture->getFKClient()->getId(), 'FK_Facture' => null, 'FK_Etat' => $etatRepository->findOneBy(['Etat' => 'Terminé'])->getId()]);

            // Boucle sur chaque intervention pour récupérer l'identifiant de l'intervention du client à facturé puis concatène ces identifiants dans un tableau
            $tabIdInterventions = [];
            foreach ($liste as $key => $value){
                array_push($tabIdInterventions, $value->getId());
            }
            // Met à jour l'etat des interventions à 'Facturé' et associe le dernier n° facture aux identifiants du tableau ci-dessus
            $interventionRepository->updateInterventionByEtatAndNumFacture($tabIdInterventions, $etatRepository->findOneBy(['Etat' => 'Facturé'])->getId(), $idFacture);

            // Redirection de la page vers la route "facture_index"
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
                die(var_dump($liste));
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
