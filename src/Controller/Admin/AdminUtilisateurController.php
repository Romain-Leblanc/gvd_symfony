<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use App\Form\Admin\AdminAjoutUtilisateurType;
use App\Form\Admin\AdminDetailUtilisateurType;
use App\Form\Admin\AdminModificationUtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/", name="utilisateur_admin_index", methods={"GET"})
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
        $lesUtilisateurs = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            $limite
        );

        return $this->render('admin/admin_utilisateur/index.html.twig', [
            'lesUtilisateurs' => $lesUtilisateurs,
            'choixListe' => $choixListe
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
            $request->getSession()->getFlashBag()->add('utilisateur', 'Cet utilisateur n\'existe pas.');
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
            $request->getSession()->getFlashBag()->add('utilisateur', 'Cet utilisateur n\'existe pas.');
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
            $request->getSession()->getFlashBag()->add('utilisateur', 'Cet utilisateur n\'existe pas.');
        }
        elseif ($this->isCsrfTokenValid('delete'.$unUtilisateur->getId(), $request->request->get('_token'))) {
            // Vérifie le token puis supprime l'utilisateur
            $utilisateurRepository->remove($unUtilisateur, true);
        }

        return $this->redirectToRoute('utilisateur_admin_index');
    }
}
