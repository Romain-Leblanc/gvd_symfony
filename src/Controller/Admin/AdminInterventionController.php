<?php

namespace App\Controller\Admin;

use App\Form\Admin\AdminDetailInterventionType;
use App\Repository\InterventionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminInterventionController extends AbstractController
{
    /**
     * @Route("/admin/intervention", name="intervention_admin_index")
     */
    public function index(InterventionRepository $interventionRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Définit le tableau des choix du nombre de résultat
        // ainsi que la valeur par défaut
        $choixListe = [25, 50, 100];
        $limite = 25;

        // Récupère la requête puisque KnpPaginator s'occupe des données lui-même
        $donnees = $interventionRepository->createQueryBuilder('i')
            ->join('i.fk_client', 'c')
            ->join('i.fk_vehicule', 'v')
            ->join('v.fk_marque', 'ma')
            ->join('v.fk_modele', 'mo')
            ->join('i.fk_etat', 'e')
        ;
        // Récupère le paramètre de limite de résultat s'il a été définit dans l'URL
        if($request->query->getInt('max') > 0
            && is_integer($request->query->getInt('max'))
            && in_array($request->query->getInt('max'), $choixListe)
        ) {
            $limite = $request->query->getInt('max');
            $donnees = $donnees->setMaxResults($limite)->getQuery();
        }

        // Traitement des données par KnpPaginator
        $lesInterventions = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            $limite
        );

        return $this->render('admin/admin_intervention/index.html.twig', [
            'lesInterventions' => $lesInterventions,
            'choixListe' => $choixListe
        ]);
    }

    /**
     * @Route("/admin/intervention/detail/{id}", name="intervention_admin_detail")
     */
    public function detail(int $id, InterventionRepository $interventionRepository, Request $request): Response
    {
        $uneIntervention = $interventionRepository->find($id);

        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($id == 0 || $uneIntervention == null) {
            $request->getSession()->getFlashBag()->add('intervention', 'Cette intervention n\'existe pas.');
            return $this->redirectToRoute('intervention_admin_index');
        }

        $form = $this->createForm(AdminDetailInterventionType::class, $uneIntervention);

        return $this->render('admin/admin_intervention/detail.html.twig', [
            'errors' => $form->getErrors(true),
            'formDetailIntervention' => $form->createView()
        ]);
    }
}
