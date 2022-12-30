<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Facture;
use App\Entity\Intervention;
use App\Entity\Vehicule;
use App\Form\AjoutFactureType;
use App\Form\EnvoiFactureType;
use App\Form\FiltreTable\FiltreTableFactureType;
use App\Form\ModificationFactureType;
use App\Repository\ClientRepository;
use App\Repository\EtatRepository;
use App\Repository\FactureRepository;
use App\Repository\InterventionRepository;
use App\Repository\TVARepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Dompdf\Dompdf;
use Dompdf\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Parsing\Html;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Mime\MimeTypesInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Routing\Annotation\Route;

class FactureController extends AbstractController
{
    /**
     * @Route("/facture", name="facture_index")
     */
    public function index(FactureRepository  $factureRepository, Request $request): Response
    {
        $lesFactures = $factureRepository->findAll();

        $form = $this->createForm(FiltreTableFactureType::class, $lesFactures);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données du formulaire de recherche
            $data = $request->request->get('filtre_table_facture');
            $filtre = [];
            // Vérifie si un filtre a été saisi puis définit ses valeurs
            if ($data['id_facture'] !== "") { $filtre['id'] = (int) $data['id_facture']; }
            if ($data['date_facture'] !== "") { $filtre['date_facture'] = new DateTime($data['date_facture']); }
            if ($data['client'] !== "") { $filtre['fk_client'] = (int) $data['client']; }
            if ($data['montant_ht'] !== "") { $filtre['montant_ht'] = $data['montant_ht']; }
            // Si un filtre a été saisi, on récupère les nouvelles valeurs
            if (isset($filtre)) {
                $lesFactures = $factureRepository->findBy($filtre);
            }
        }

        return $this->render('facture/index.html.twig', [
            'lesFactures' => $lesFactures,
            'formFiltreTable' => $form->createView()
        ]);
    }

    /**
     * @Route("/facture/ajouter", name="facture_ajouter")
     * @throws \Spipu\Html2Pdf\Exception\Html2PdfException
     */
    public function ajouter(InterventionRepository $interventionRepository, EtatRepository $etatRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Retourne la liste des interventions qui sont terminées
        $listeInterventions = $interventionRepository->findBy(['fk_etat' => $etatRepository->findOneBy(['etat' => 'Terminé'])->getId()]);

        // Si aucune intervention est terminée (et donc n'a pas besoin d'être facturé), alors on renvoie un message puis une redirection
        if(empty($listeInterventions)){
            $request->getSession()->getFlashBag()->add('facture', 'Aucun intervention à facturée.');
            return $this->redirectToRoute("facture_index");
        }

        // Création de l'objet Facture(), génération du formulaire d'ajout d'une Facture avec l'objet Facture et manipulation des données de l'objet Request
        $uneFacture = new Facture();
        $form = $this->createForm(AjoutFactureType::class, $uneFacture);
        $form->handleRequest($request);

        // Si le formulaire a bien été soumis et est validé
        if ($form->isSubmitted() && $form->isValid()){
            $idClient = $uneFacture->getFkClient()->getId();
            $idIntervention = $interventionRepository->findOneBy(
                ['fk_client' => $uneFacture->getFKClient()->getId(),
                    'fk_facture' => null,
                    'fk_etat' => $etatRepository->findOneBy(['etat' => 'Terminé'])->getId()],
                ['id' => 'DESC']
            )->getFkClient()->getId();
            // Si le client des interventions est différent de celui de la facture qui sera ajoutée,
            // on génère une erreur
            if($idClient !== $idIntervention) {
                $message = "Le client de ces interventions n'est pas le même que celui de la facture.";
                return $this->render('facture/ajout.html.twig', [
                    'errors' => $form->addError(new FormError($message))->getErrors(true),
                    'formAjoutFacture' => $form->createView(),
                    'listeInterventions' => $listeInterventions
                ]);
            }
            // On persiste l'objet Facture dans l'entité Facture
            $entityManager->persist($uneFacture);
            $entityManager->flush();

            // Equivalent de la fonction lastInsertId() qui permet de récupérer le dernier identifiant inséré dans la table facture
            $idFacture = $uneFacture->getId();

            // Récupère la liste des interventions terminées du client qui ne sont pas facturées
            $liste = $interventionRepository->findBy(['fk_client' => $uneFacture->getFKClient()->getId(), 'fk_facture' => null, 'fk_etat' => $etatRepository->findOneBy(['etat' => 'Terminé'])->getId()]);

            // Boucle sur chaque intervention pour récupérer l'identifiant de l'intervention du client à facturé puis concatène ces identifiants dans un tableau
            $tabIdInterventions = [];
            foreach ($liste as $value){
                array_push($tabIdInterventions, $value->getId());
            }

            // Met à jour l'etat des interventions à 'Facturé' et associe le dernier n° facture aux identifiants du tableau ci-dessus
            $interventionRepository->updateInterventionByEtatAndNumFacture($tabIdInterventions, $etatRepository->findOneBy(['etat' => 'Facturé'])->getId(), $idFacture);

            // Génère et enregistre le PDF
            $this->genererPdf($uneFacture, $listeInterventions);

            // Redirection de la page vers le tableau principal
            return $this->redirectToRoute('facture_index');
        }

        return $this->render('facture/ajout.html.twig', [
            'errors' => $form->getErrors(true),
            'formAjoutFacture' => $form->createView(),
            'listeInterventions' => $listeInterventions
        ]);
    }

    /**
     * @Route("/facture/modifier/{id}", name="facture_modifier", defaults={"id" = 0})
     */
    public function modifier(int $id, FactureRepository $factureRepository, InterventionRepository $interventionRepository, TVARepository $TVARepository, ClientRepository $clientRepository, Request $request): Response
    {
        $uneFacture = $factureRepository->find($id);
        $listeInterventions = $interventionRepository->findBy(['fk_client' => $uneFacture->getFKClient()->getId(), 'fk_facture' => $uneFacture->getId()]);

        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($id == 0 || $uneFacture == null) {
            $request->getSession()->getFlashBag()->add('facture', 'Cette facture n\'existe pas.');
            return $this->redirectToRoute('facture_index');
        }

        // Récupère les données du client et du taux TVA de la facture
        // Utilisés après la soumission du formulaire puisque les champs "client" et "facture" sont désactivés
        $client = $uneFacture->getFkClient();
        $taux = $uneFacture->getFkTaux();

        $form = $this->createForm(ModificationFactureType::class, $uneFacture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Met à jour la facture
            $factureRepository->updateFacture($uneFacture);

            // Définit les valeurs de l'objet Facture avec les variables précédentes contenant ces informations
            $uneFacture->setFkClient($client);
            $uneFacture->setFkTaux($taux);

            // Récupère les interventions de la facture
            $listeInterventions = $interventionRepository->findBy(['fk_facture' => $uneFacture->getId()]);

            // Génère le PDF de la facture avec les nouvelles informations
            $this->genererPdf($uneFacture, $listeInterventions);

            return $this->redirectToRoute('facture_index');
        }

        return $this->render('facture/modification.html.twig', [
            'errors' => $form->getErrors(true),
            'formModificationFacture' => $form->createView(),
            'listeInterventions' => $listeInterventions
        ]);
    }

    /**
     * @Route("/facture/envoyer/{id}", name="facture_envoyer", defaults={"id" = 0})
     */
    public function envoyer(int $id, FactureRepository $factureRepository, Request $request, MailerInterface $mailer): Response
    {
        $uneFacture = $factureRepository->find($id);

        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if ($id == 0 || $uneFacture == null) {
            $request->getSession()->getFlashBag()->add('facture', 'Cette facture n\'existe pas.');
            return $this->redirectToRoute('facture_index');
        }

        // Vérifie et récupère le fichier PDF de la facture
        $fichier = $id.'.pdf';
        $chemin = $this->getParameter('kernel.project_dir')."/public";
        $cheminCompletFacture = $chemin."/pdf_facture/".$fichier;

        // Ce formulaire n'est pas relié à l'entité Facture puisqu'il est différent de cette entité (expediteur, destinataire...)
        $form = $this->createForm(EnvoiFactureType::class, ["uneFacture" => $uneFacture, "cheminLogo" => $request->getSchemeAndHttpHost()]);
        $form->handleRequest($request);

        // Envoi du mail
        if ($form->isSubmitted() && $form->isValid()) {
            $email = (new Email())
                ->from($form->getData()['expediteur'])
                ->to($form->getData()['destinataire'])
                ->subject($form->getData()['objet'])
                ->html($form->getData()['message']);
            // Si le fichier pour la pièce jointe existe, on l'ajoute au mail
            if(file_exists($cheminCompletFacture)) {
                $email->attachFromPath($cheminCompletFacture, "Facture n°".$id.".pdf");
            }

            try {
                $mailer->send($email);
                $request->getSession()->getFlashBag()->add('facture_mail_success', 'Le mail a été envoyé.');
                return $this->redirectToRoute('facture_index');
            } catch (\Exception $t) {
                return new Response($t->getMessage());
            }
        }

        return $this->render('facture/envoi.html.twig', [
            'errors' => $form->getErrors(true),
            'formEnvoiFacture' => $form->createView(),
            "fichier" => file_exists($cheminCompletFacture),
            'uneFacture' => $uneFacture, // Les informations de cette facture seront affichées avec le template twig
        ]);
    }

    /**
     * @Route("/facture/telecharger/{id}", name="facture_telecharger", defaults={"id" = 0})
     */
    public function telecharger(int $id, FactureRepository $factureRepository, Request $request): Response
    {
        // Récupère toutes les infos sur la facture
        $uneFacture = $factureRepository->findOneBy(['id' => $id]);

        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($id == 0 || $uneFacture == null) {
            $request->getSession()->getFlashBag()->add('facture', 'Cette facture n\'existe pas.');
            return $this->redirectToRoute('facture_index');
        }

        // Récupération puis affichage du PDF de la facture
        $chemin = $this->getParameter('kernel.project_dir')."/public/pdf_facture/".$id.".pdf";
        return $this->file($chemin, null, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    public function genererPdf(Facture $facture, array $intervention) {
        // Récupération du logo pour le PDF
        $logo = $this->getParameter('kernel.project_dir')."/public/images/logo_64.png";

        // Génération du contenu du PDF
        $html = $this->renderView('facture/donnees_pdf.html.twig', [
            'uneFacture' => $facture,
            'listeInterventions' => $intervention,
            'logo' => $logo
        ]);

        // Génération nom du fichier avec l'emplacement de sauvegarde du PDF sur le disque
        $fichier = $facture->getId().'.pdf';
        $chemin = $this->getParameter('kernel.project_dir')."/public/pdf_facture";
        $cheminComplet = $chemin."/".$fichier;

        // Génère le PDF final
        $pdf = new Html2Pdf('P', 'A4', 'fr');
        $pdf->pdf->setTitle("Facture n°".$facture->getId());
        $pdf->writeHTML($html);

        // Enregiste le PDF dans un fichier dans le dossier des factures
        $data = $pdf->pdf->getPDFData();
        file_put_contents($cheminComplet, $data);
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
                return $this->json(['donnees' => $liste]);
            }
            else {
                $request->getSession()->getFlashBag()->add('facture', 'Cet accès est restreint.');
                return $this->redirectToRoute('facture_index');
            }
        }
        else {
            $request->getSession()->getFlashBag()->add('facture', 'Cet accès est restreint.');
            return $this->redirectToRoute('facture_index');
        }
    }
}
