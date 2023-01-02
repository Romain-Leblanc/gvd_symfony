<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use App\Form\Admin\AdminAjoutUtilisateurType;
use App\Form\Admin\AdminModificationUtilisateurType;
use App\Form\FiltreTable\Admin\AdminFiltreTableUtilisateurType;
use App\Repository\UtilisateurRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/utilisateur")
 */
class AdminUtilisateurController extends AbstractController
{
    /**
     * @Route("/", name="utilisateur_admin_index", methods={"GET", "POST"})
     */
    public function index(UtilisateurRepository $utilisateurRepository, PaginatorInterface $paginator, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Définit le tableau des choix du nombre de résultat
        // ainsi que la valeur par défaut
        $choixListe = [25, 50, 100];
        $limite = 25;

        // Récupère la requête puisque KnpPaginator s'occupe des données lui-même
        $donnees = $utilisateurRepository->createQueryBuilder('u');

        // Récupère le paramètre de limite de résultat s'il a été définit dans l'URL
        if($request->query->getInt('max') > 0
            && is_integer($request->query->getInt('max'))
            && in_array($request->query->getInt('max'), $choixListe)
        ) {
            $limite = $request->query->getInt('max');
            $donnees = $donnees->setMaxResults($limite)->getQuery();
        }
        elseif ($request->query->get('sort') == "u.roles" && in_array($request->query->get('direction'), ['asc', 'desc'])) {
            // Le tri par role utilisateur avec createQueryBuilder ne fonctionne pas
            // donc on utilise une requête manuelle
            $sql = "SELECT u FROM App\Entity\Utilisateur u ORDER BY CONCAT('%', u.roles, '%') ".$request->query->get('direction');
            $donnees = $entityManager->createQuery($sql);
        }

        // Traitement des données par KnpPaginator
        $lesUtilisateursPagination = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            $limite
        );
        // Valeurs par défaut des résultats des filtres
        $lesUtilisateursForm = $lesUtilisateursPagination->getItems();

        $form = $this->createForm(AdminFiltreTableUtilisateurType::class, $lesUtilisateursPagination->getItems());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données du formulaire de recherche
            $data = $request->request->get('admin_filtre_table_utilisateur');
            $query = $utilisateurRepository->createQueryBuilder('u');
            // Vérifie si un filtre a été saisi puis définit ses valeurs
            if ($data['id_utilisateur'] !== "" || $data['utilisateur'] !== "" || $data['roles'] !== "") {
                if ($data['id_utilisateur'] !== "" || $data['utilisateur'] !== "") {
                    if ($data['id_utilisateur']) { (int) $value = $data['id_utilisateur']; }
                    else { $value = (int) $data['utilisateur']; }
                    $query = $query
                        ->andWhere('u.id = :id')
                        ->setParameter('id', $value)
                    ;
                }
                if ($data['roles'] !== "") {
                    $query = $query
                        ->andWhere('u.roles LIKE :role')
                        ->setParameter('role', "%".$data['roles']."%")
                    ;
                }
                $lesUtilisateursForm = $query->getQuery()->getResult();
            }
        }

        return $this->render('admin/admin_utilisateur/index.html.twig', [
            // Données pour Knppaginator
            'lesUtilisateursPagination' => $lesUtilisateursPagination,
            // Données pour le formulaire et tableau
            'lesUtilisateursForm' => $lesUtilisateursForm,
            'choixListe' => $choixListe,
            'formFiltreTable' => $form->createView()
        ]);
    }

    /**
     * @Route("/detail/{id}", name="utilisateur_admin_detail", methods={"GET"})
     */
    public function detail(int $id, UtilisateurRepository $utilisateurRepository, Request $request): Response
    {
        $unUtilisateur = $utilisateurRepository->find($id);

        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($id == 0 || $unUtilisateur == null) {
            $this->addFlash('utilisateur', 'Cet utilisateur n\'existe pas.');
            return $this->redirectToRoute('utilisateur_admin_index');
        }
        elseif(in_array('ROLE_SUPER_ADMIN', $unUtilisateur->getRoles())) {
            // Si l'identifiant existe dans la table correspondante, on génère un message d'erreur
            $this->addFlash('utilisateur', 'Impossible de voir le détail de cet utilisateur.');
            return $this->redirectToRoute('utilisateur_admin_index');
        }

        return $this->render('admin/admin_utilisateur/show.html.twig', [
            'unUtilisateur' => $unUtilisateur
        ]);
    }

    /**
     * @Route("/ajouter", name="utilisateur_admin_ajouter", methods={"GET", "POST"})
     */
    public function ajouter(Request $request, UtilisateurRepository $utilisateurRepository, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $unUtilisateur = new Utilisateur();
        $form = $this->createForm(AdminAjoutUtilisateurType::class, $unUtilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $unUtilisateur->setPassword($passwordEncoder->hashPassword($unUtilisateur, $form->get('password')->getData()));
            $utilisateurRepository->add($unUtilisateur, true);

            return $this->redirectToRoute('utilisateur_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/admin_utilisateur/new.html.twig', [
            'errors' => $form->getErrors(true),
            'formAjoutUtilisateur' => $form,
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="utilisateur_admin_modifier", methods={"GET", "POST"})
     */
    public function modifier(int $id, Request $request, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $unUtilisateur = $utilisateurRepository->find($id);

        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($id == 0 || $unUtilisateur == null) {
            $this->addFlash('utilisateur', 'Cet utilisateur n\'existe pas.');
            return $this->redirectToRoute('utilisateur_admin_index');
        }
        elseif(in_array('ROLE_SUPER_ADMIN', $unUtilisateur->getRoles())) {
            // Si l'identifiant existe dans la table correspondante, on génère un message d'erreur
            $this->addFlash('utilisateur', 'Cet utilisateur n\'est pas modifiable.');
            return $this->redirectToRoute('utilisateur_admin_index');
        }

        $form = $this->createForm(AdminModificationUtilisateurType::class, $unUtilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mise à jour des infos de l'utilisateur dont l'encodage du mot de passe
            if($form->get('password')->getData() != null && $form->get('password')->getData() != "") {
                $unUtilisateur->setPassword($passwordEncoder->hashPassword($unUtilisateur, $form->get('password')->getData()));
            }
            $entityManager->persist($unUtilisateur);
            $entityManager->flush();
            // Si l'utilisateur modifié est celui actuellement connecté, on le force à se reconnecter
            if ($unUtilisateur->getId() === $this->getUser()->getId()) {
                $this->addFlash('utilisateur', 'Veuillez vous reconnecter.');
                return $this->redirectToRoute('app_deconnexion');
            }
            return $this->redirectToRoute('utilisateur_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/admin_utilisateur/edit.html.twig', [
            'errors' => $form->getErrors(true),
            'formModificationUtilisateur' => $form
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="utilisateur_admin_supprimer", methods={"GET", "POST"})
     */
    public function supprimer(int $id, Request $request, UtilisateurRepository $utilisateurRepository): Response
    {
        $unUtilisateur = $utilisateurRepository->find($id);

        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on génère une erreur
        if($id == 0 || $unUtilisateur == null) {
            $this->addFlash('utilisateur', 'Cet utilisateur n\'existe pas.');
        }
        elseif(in_array('ROLE_SUPER_ADMIN', $unUtilisateur->getRoles())) {
            // Si l'identifiant existe dans la table correspondante, on génère un message d'erreur
            $this->addFlash('utilisateur', 'Cet utilisateur n\'est pas supprimable.');
            return $this->redirectToRoute('utilisateur_admin_index');
        }
        elseif ($this->isCsrfTokenValid('delete'.$unUtilisateur->getId(), $request->request->get('_token'))) {
            // Si l'utilisateur supprimé est celui actuellement connecté, on le force à se reconnecter
            if ($unUtilisateur->getId() === $this->getUser()->getId()) {
                // Réinitialise la session utilisateur
                $this->container->get('security.token_storage')->setToken(null);
                // Supprime l'utilisateur et redirige vers le formulaire de connexion
                $utilisateurRepository->remove($unUtilisateur, true);
                return $this->redirectToRoute('app_deconnexion');
            }
            else {
                // Vérifie le token puis supprime cet élément
                $utilisateurRepository->remove($unUtilisateur, true);
            }
        }
        return $this->redirectToRoute('utilisateur_admin_index');
    }
}
