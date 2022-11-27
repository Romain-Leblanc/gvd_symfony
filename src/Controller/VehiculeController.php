<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\AjoutVehiculeType;
use App\Form\ModificationVehiculeType;
use App\Repository\InterventionRepository;
use App\Repository\ModeleRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VehiculeController extends AbstractController
{
    /**
     * @Route("/vehicule", name="vehicule_index")
     */
    public function index(VehiculeRepository $vehiculeRepository): Response
    {
        $lesVehicules = $vehiculeRepository->findAll();

        return $this->render('vehicule/index.html.twig', [
            'lesVehicules' => $lesVehicules
        ]);
    }

    /**
     * @Route("/vehicule/ajouter", name="vehicule_ajouter")
     */
    public function ajouter(Request $request, EntityManagerInterface $entityManager): Response
    {
        $unVehicule = new Vehicule();
        $form = $this->createForm(AjoutVehiculeType::class, $unVehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($unVehicule);
            $entityManager->flush();

            return $this->redirectToRoute('vehicule_index');
        }

        return $this->render('vehicule/ajout.html.twig', [
            'formAjoutVehicule' => $form->createView(),
            'errors' => $form->getErrors(true),
        ]);
    }

    /**
     * @Route("/vehicule/modifier/{id}", name="vehicule_modifier")
     */
    public function modifier(int $id, VehiculeRepository $vehiculeRepository, InterventionRepository $interventionRepository, Request $request): Response
    {
        $unVehicule = $vehiculeRepository->find($id);
        // Si le véhicule est déjà dans une intervention, on ne peut pas modifier à quel client appartient ce véhicule, ni la marque et le modèle.
        $options = $interventionRepository->findBy(['fk_vehicule' => $unVehicule->getId()]);
        $form = $this->createForm(ModificationVehiculeType::class, $unVehicule, ["intervention" => $options]);
        $form->handleRequest($request);
        dd($request->request->all(), $form->getErrors(), $form->isValid());
//        dd($form->getData(), $request->request->get('modification_vehicule'), $form->getErrors(true));
//        dd($request, $form->getErrors(true), array_merge($request->request->all()));

        if ($form->isSubmitted() && $form->isValid()) {
            dd($request->request, $form->isSubmitted(), $form->isValid());

            return $this->redirectToRoute('vehicule_index');
        }

        return $this->render('vehicule/modification.html.twig', [
            'errors' => $form->getErrors(true),
            'formModificationVehicule' => $form->createView()
        ]);
    }

    /**
     * @Route("/vehicule/infos", name="vehicule_infos")
     */
    public function infos(ModeleRepository $modeleRepository, Request $request)
    {
        $id = (int) $request->request->get('marqueID');
        if (!empty($id) && $id !== 0) {
            // Renvoi la liste des modèles de la marque de voiture pour Ajax
            $liste = $modeleRepository->findBy(['fk_marque' => $id]);
            return $this->json(['donnees' => $liste]);
        }
        else {
            return $this->json(['donnees' => ""]);
        }
    }
}
