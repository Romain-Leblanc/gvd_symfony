<?php

namespace App\Controller\Admin;

use App\Entity\Marque;
use App\Entity\Modele;
use App\Form\Admin\AdminAjoutMarqueType;
use App\Form\Admin\AdminModificationMarqueType;
use App\Form\FiltreTable\Admin\AdminFiltreTableMarqueType;
use App\Repository\MarqueRepository;
use App\Repository\ModeleRepository;
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
 * @Route("/admin/marque")
 */
class AdminMarqueController extends AbstractController
{
    /**
     * @Route("/", name="marque_admin_index", methods={"GET", "POST"})
     */
    public function index(MarqueRepository $marqueRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Définit le tableau des choix du nombre de résultat
        // ainsi que la valeur par défaut
        $choixListe = [25, 50, 100];
        $limite = 25;

        // Récupère la requête puisque KnpPaginator s'occupe des données lui-même
        $donnees = $marqueRepository->createQueryBuilder('ma')
            ->select('ma.id')
            ->addSelect('ma.marque')
            ->addSelect('COUNT(mo.id) as nombre')
            ->leftJoin(Modele::class, 'mo', Join::WITH, 'ma.id = mo.fk_marque')
            ->groupBy('ma.id')
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
        $lesMarquesPagination = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            $limite
        );
        // Valeurs par défaut des résultats des filtres
        $lesMarquesForm = $lesMarquesPagination->getItems();

        $form = $this->createForm(AdminFiltreTableMarqueType::class, $marqueRepository->findAll());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données du formulaire de recherche
            $data = $request->request->get('admin_filtre_table_marque');
            // Vérifie si un filtre a été saisi puis définit ses valeurs
            if ($data['id_marque'] !== "" || $data['marque'] !== "") {
                if ($data['id_marque']) { $value = $data['id_marque']; }
                else { $value = $data['marque']; }
                $lesMarquesForm = $marqueRepository->createQueryBuilder('ma')
                    ->select('ma.id')
                    ->addSelect('ma.marque')
                    ->addSelect('COUNT(mo.id) as nombre')
                    ->leftJoin(Modele::class, 'mo', Join::WITH, 'ma.id = mo.fk_marque')
                    ->where('ma.id = :value')
                    ->setParameter('value', $value)
                    ->groupBy('ma.id')
                    ->getQuery()
                    ->getResult()
                ;
            }
        }

        return $this->render('admin/admin_marque/index.html.twig', [
            // Données pour Knppaginator
            'lesMarquesPagination' => $lesMarquesPagination,
            // Données pour le formulaire et tableau
            'lesMarquesForm' => $lesMarquesForm,
            'choixListe' => $choixListe,
            'formFiltreTable' => $form->createView()
        ]);
    }

    /**
     * @Route("/ajouter", name="marque_admin_ajouter", methods={"GET", "POST"})
     */
    public function ajouter(Request $request, MarqueRepository $marqueRepository): Response
    {
        $uneMarque = new Marque();
        $form = $this->createForm(AdminAjoutMarqueType::class, $uneMarque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Met la marque en majuscule
            $uneMarque->setMarque(strtoupper($uneMarque->getMarque()));
            $marqueRepository->add($uneMarque, true);

            return $this->redirectToRoute('modele_admin_ajouter', ['idmarque' => $uneMarque->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/admin_marque/new.html.twig', [
            'errors' => $form->getErrors(true),
            'formAjoutMarque' => $form,
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="marque_admin_modifier", methods={"GET", "POST"})
     */
    public function modifier(int $id, Request $request, MarqueRepository $marqueRepository, EntityManagerInterface $entityManager): Response
    {
        $uneMarque = $marqueRepository->find($id);

        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($id == 0 || $uneMarque == null) {
            $request->getSession()->getFlashBag()->add('marque', 'Cette marque n\'existe pas.');
            return $this->redirectToRoute('marque_admin_index');
        }

        $form = $this->createForm(AdminModificationMarqueType::class, $uneMarque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // La marque en majuscule
            $uneMarque->setMarque(strtoupper($uneMarque->getMarque()));
            $entityManager->persist($uneMarque);
            $entityManager->flush();
            return $this->redirectToRoute('marque_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/admin_marque/edit.html.twig', [
            'errors' => $form->getErrors(true),
            'formModificationMarque' => $form
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="marque_admin_supprimer", methods={"GET", "POST"})
     */
    public function supprimer(int $id, Request $request, MarqueRepository $marqueRepository, ModeleRepository $modeleRepository): Response
    {
        $uneMarque = $marqueRepository->find($id);

        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($id == 0 || $uneMarque == null) {
            $request->getSession()->getFlashBag()->add('marque', 'Cette marque n\'existe pas.');
        }
        elseif(!empty($modeleRepository->findBy(['fk_marque' => $uneMarque->getId()]))) {
            $request->getSession()->getFlashBag()->add('marque', 'Cette marque n\'est pas supprimable.');
        }
        elseif (
            $this->isCsrfTokenValid('delete'.$uneMarque->getId(), $request->request->get('_token'))
            && empty($modeleRepository->findBy(['fk_marque' => $uneMarque->getId()]))
        ) {
            // Vérifie le token puis supprime cet élément
            $marqueRepository->remove($uneMarque, true);
        }

        return $this->redirectToRoute('marque_admin_index');
    }
}
