<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\AjoutVehiculeType;
use App\Repository\ModeleRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     * @Route("/vehicule/infos", name="vehicule_infos")
     */
    public function infos(ModeleRepository $modeleRepository, Request $request)
    {
        $id = (int) $request->request->get('marqueID');
        if (!empty($id)) {
            // Renvoi la liste des modèles de la marque de voiture pour Ajax
            $liste = $modeleRepository->findBy(['fk_marque' => $id]);
            return $this->json(['donnees' => $liste]);
        }
        else {
            return $this->json(['donnees' => ""]);
        }
    }
}
