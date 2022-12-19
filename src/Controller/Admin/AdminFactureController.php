<?php

namespace App\Controller\Admin;

use App\Repository\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminFactureController extends AbstractController
{
    /**
     * @Route("/admin/facture", name="facture_admin_index")
     */
    public function index(FactureRepository $factureRepository): Response
    {
        // Récupère l'année actuelle
        $annee = date('Y');

        // Tableau qui récupère le mois traduit en français
        $tableauMois = [
            'Janvier' => 'January',
            'Février' => 'February',
            'Mars' => 'March',
            'Avril' => 'April',
            'Mai' => 'May',
            'Juin' => 'June',
            'Juillet' => 'July ',
            'Août' => 'August',
            'Septembre' => 'September',
            'Octobre' => 'October',
            'Novembre' => 'November',
            'Décembre' => 'December'
        ];
        $tableauResultat = [];

        // Boucle sur chaque mois pour définir sa traduction et son montant HT si égal à zéro.
        // Le groupement par mois de l'année n'est pas utilisé parce que la requête ne retournera rien
        // si aucune facture d'un certain mois n'existe pas 
        foreach ($tableauMois as $mois) {
            // Récupère les infos des factures du mois 
            $resultat = $factureRepository->findByMois($annee, $mois);
            // Traduit le mois de l'année si il est présent dans le tableau des traductions 
            if(array_search($mois, $tableauMois)) {
                $resultat['mois'] = array_search($mois, $tableauMois);
            }
            // Définit
            if(is_null($resultat['montant'])) {
                $resultat['nombre'] = 0;
                $resultat['montant'] = 0;
            }
            array_push($tableauResultat, $resultat);
        }

        return $this->render('admin/admin_facture/index.html.twig', [
            'lesMoisFactures' => $tableauResultat,
            'annee' => $annee
        ]);
    }
}
