<?php

namespace App\Controller\Admin;

use App\Entity\Marque;
use App\Entity\Modele;
use App\Entity\Vehicule;
use App\Form\Admin\AdminAjoutModeleType;
use App\Form\Admin\AdminModificationModeleType;
use App\Form\FiltreTable\Admin\AdminFiltreTableModeleType;
use App\Repository\MarqueRepository;
use App\Repository\ModeleRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/modele")
 */
class AdminModeleController extends AbstractController
{
    /**
     * @Route("/", name="modele_admin_index", methods={"GET", "POST"})
     */
    public function index(ModeleRepository $modeleRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Définit le tableau des choix du nombre de résultat
        // ainsi que la valeur par défaut
        $choixListe = [25, 50, 100];
        $limite = 25;

        // Récupère la requête puisque KnpPaginator s'occupe des données lui-même
        $donnees = $modeleRepository->createQueryBuilder('mo')
            ->select('mo')
            ->addSelect('COUNT(v.fk_modele) as nombreVehicule')
            ->leftJoin(Vehicule::class, 'v', Join::WITH, 'v.fk_modele = mo.id')
            ->join('mo.fk_marque', 'ma')
            ->groupBy('mo.id')
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
        $lesModelesPagination = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            $limite
        );
        // Valeurs par défaut des résultats des filtres
        $lesModelesForm = $lesModelesPagination->getItems();

        $form = $this->createForm(AdminFiltreTableModeleType::class, $modeleRepository->findAll());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données du formulaire de recherche
            $data = $request->request->get('admin_filtre_table_modele');
            $query = $modeleRepository->createQueryBuilder('mo')
                ->select('mo')
                ->addSelect('COUNT(v.fk_modele) as nombreVehicule')
                ->leftJoin(Vehicule::class, 'v', Join::WITH, 'v.fk_modele = mo.id')
                ->join('mo.fk_marque', 'ma')
            ;
            // Vérifie si un filtre a été saisi puis définit ses valeurs
            if ($data['id_modele'] !== "" || $data['modele'] !== "" || $data['marque'] !== "") {
                if ($data['id_modele'] !== "" || $data['modele'] !== "") {
                    if ($data['id_modele']) { $value = $data['id_modele']; }
                    else { $value = $data['modele']; }
                    $query = $query
                        ->andWhere('mo.id = :id')
                        ->setParameter('id', $value)
                    ;
                }
                if ($data['marque'] !== "") {
                    $query = $query
                        ->andWhere('mo.fk_marque = :id_marque')
                        ->setParameter('id_marque', $data['marque'])
                    ;
                }
                $lesModelesForm = $query->groupBy('mo.id')->getQuery()->getResult();
            }
        }

        return $this->render('admin/admin_modele/index.html.twig', [
            // Données pour Knppaginator
            'lesModelesPagination' => $lesModelesPagination,
            // Données pour le formulaire et tableau
            'lesModelesForm' => $lesModelesForm,
            'choixListe' => $choixListe,
            'formFiltreTable' => $form->createView()
        ]);
    }

    /**
     * @Route("/ajouter", name="modele_admin_ajouter", methods={"GET", "POST"})
     */
    public function ajouter(Request $request, ModeleRepository $modeleRepository, MarqueRepository $marqueRepository): Response
    {
        $unModele = new Modele();
        // Récupère l'identifiant de la marque insérée qui nécessite un modèle
        $idMarque = $request->get('idmarque');
        // Si l'identifiant de la marque est présent,
        // on redirige vers le formulaire d'ajout d'un modèle
        if($idMarque > 0 && $idMarque != null) {
            $uneMarque = $marqueRepository->find($idMarque);
            $unModele->setFkMarque($uneMarque);
        }
        $form = $this->createForm(AdminAjoutModeleType::class, $unModele);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Le modèle en majuscule
            $unModele->setModele(strtoupper($unModele->getModele()));
            $modeleRepository->add($unModele, true);

            return $this->redirectToRoute('modele_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/admin_modele/new.html.twig', [
            'errors' => $form->getErrors(true),
            'formAjoutModele' => $form,
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="modele_admin_modifier", methods={"GET", "POST"})
     */
    public function modifier(int $id, Request $request, ModeleRepository $modeleRepository, VehiculeRepository $vehiculeRepository, EntityManagerInterface $entityManager): Response
    {
        $unModele = $modeleRepository->find($id);

        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($id == 0 || $unModele == null) {
            $this->addFlash('modele', 'Ce modèle n\'existe pas.');
            return $this->redirectToRoute('modele_admin_index');
        }
        elseif(!empty($vehiculeRepository->findBy(['fk_marque' => $unModele->getFkMarque()->getId(), 'fk_modele' => $unModele->getId()]))) {
            // Si l'identifiant existe dans la table correspondante, on génère un message d'erreur
            $this->addFlash('modele', 'Ce modèle n\'est pas modifiable.');
            return $this->redirectToRoute('modele_admin_index');
        }

        $form = $this->createForm(AdminModificationModeleType::class, $unModele);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Le modèle en majuscule
            $unModele->setModele(strtoupper($unModele->getModele()));
            $entityManager->persist($unModele);
            $entityManager->flush();
            return $this->redirectToRoute('modele_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/admin_modele/edit.html.twig', [
            'errors' => $form->getErrors(true),
            'formModificationModele' => $form
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="modele_admin_supprimer", methods={"GET", "POST"})
     */
    public function supprimer(int $id, Request $request, ModeleRepository $modeleRepository, VehiculeRepository $vehiculeRepository): Response
    {
        $unModele = $modeleRepository->find($id);

        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($id == 0 || $unModele == null) {
            $this->addFlash('modele', 'Ce modèle n\'existe pas.');
        }
        elseif(!empty($vehiculeRepository->findBy(['fk_marque' => $unModele->getFkMarque()->getId(), 'fk_modele' => $unModele->getId()]))) {
            // Si l'identifiant existe dans la table correspondante, on génère un message d'erreur
            $this->addFlash('modele', 'Ce modèle n\'est pas supprimable.');
        }
        elseif (
            $this->isCsrfTokenValid('delete'.$unModele->getId(), $request->request->get('_token'))
            && empty($vehiculeRepository->findBy(['fk_marque' => $unModele->getFkMarque()->getId(), 'fk_modele' => $unModele->getId()]))
        ) {
            // Vérifie le token puis supprime cet élément
            $modeleRepository->remove($unModele, true);
        }

        return $this->redirectToRoute('modele_admin_index');
    }
}
