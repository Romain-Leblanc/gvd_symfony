<?php

namespace App\Controller\Admin;

use App\Form\Admin\NombreLigneType;
use App\Repository\ModeleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminModeleController extends AbstractController
{
    /**
     * @Route("/admin/modele", name="modele_admin_index")
     */
    public function index(ModeleRepository $modeleRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Définit le tableau des choix du nombre de résultat
        // ainsi que la valeur par défaut
        $choixListe = [25, 50, 100];
        $limite = 25;

        // Récupère la requête puisque KnpPaginator s'occupe des données lui-même
        $donnees = $modeleRepository->createQueryBuilder('mo')->join('mo.fk_marque', 'ma');

        // Récupère le paramètre de limite de résultat s'il a été définit dans l'URL
        if($request->query->getInt('max') > 0
            && is_integer($request->query->getInt('max'))
            && in_array($request->query->getInt('max'), $choixListe)
        ) {
            $limite = $request->query->getInt('max');
            $donnees = $donnees->setMaxResults($limite)->getQuery();
        }

        // Traitement des données par KnpPaginator
        $lesModeles = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            $limite
        );

        return $this->render('admin/admin_modele/index.html.twig', [
            'lesModeles' => $lesModeles,
            'choixListe' => $choixListe
        ]);
    }
}
