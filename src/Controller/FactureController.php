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
        $lesFactures = $factureRepository->findBy([], ['id' => 'DESC']);;

        $form = $this->createForm(FiltreTableFactureType::class, $lesFactures);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // R??cup??re les donn??es du formulaire de recherche
            $data = $request->request->get('filtre_table_facture');
            $filtre = [];
            // V??rifie si un filtre a ??t?? saisi puis d??finit ses valeurs
            if ($data['id_facture'] !== "") { $filtre['id'] = (int) $data['id_facture']; }
            if ($data['date_facture'] !== "") { $filtre['date_facture'] = new DateTime($data['date_facture']); }
            if ($data['client'] !== "") { $filtre['fk_client'] = (int) $data['client']; }
            if ($data['montant_ht'] !== "") { $filtre['montant_ht'] = $data['montant_ht']; }
            // Si un filtre a ??t?? saisi, on r??cup??re les nouvelles valeurs
            if (isset($filtre)) {
                $lesFactures = $factureRepository->findBy($filtre, ['id' => 'DESC']);
            }
        }

        return $this->render('facture/index.html.twig', [
            'lesFactures' => $lesFactures,
            'formFiltreTable' => $form->createView()
        ]);
    }

    /**
     * @Route("/facture/ajouter", name="facture_ajouter", methods={"GET", "POST"})
     * @throws \Spipu\Html2Pdf\Exception\Html2PdfException
     */
    public function ajouter(InterventionRepository $interventionRepository, EtatRepository $etatRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Retourne la liste des interventions qui sont termin??es
        $listeInterventions = $interventionRepository->findBy(['fk_etat' => $etatRepository->findOneBy(['etat' => 'Termin??'])->getId()]);

        // Si aucune intervention est termin??e (et donc n'a pas besoin d'??tre factur??), alors on renvoie un message puis une redirection
        if(empty($listeInterventions)){
            $this->addFlash('facture', 'Aucun intervention ?? factur??e.');
            return $this->redirectToRoute("facture_index");
        }

        // Cr??ation de l'objet Facture(), g??n??ration du formulaire d'ajout d'une Facture avec l'objet Facture et manipulation des donn??es de l'objet Request
        $uneFacture = new Facture();
        $form = $this->createForm(AjoutFactureType::class, $uneFacture);
        $form->handleRequest($request);

        // Si le formulaire a bien ??t?? soumis et est valid??
        if ($form->isSubmitted() && $form->isValid()){
            $idClient = $uneFacture->getFkClient()->getId();
            $idIntervention = $interventionRepository->findOneBy(
                ['fk_client' => $uneFacture->getFKClient()->getId(),
                    'fk_facture' => null,
                    'fk_etat' => $etatRepository->findOneBy(['etat' => 'Termin??'])->getId()],
                ['id' => 'DESC']
            )->getFkClient()->getId();
            // Si le client des interventions est diff??rent de celui de la facture qui sera ajout??e,
            // on g??n??re une erreur
            if($idClient !== $idIntervention) {
                $message = "Le client de ces interventions n'est pas le m??me que celui de la facture.";
                return $this->render('facture/ajout.html.twig', [
                    'errors' => $form->addError(new FormError($message))->getErrors(true),
                    'formAjoutFacture' => $form->createView(),
                    'listeInterventions' => $listeInterventions
                ]);
            }
            // Si seulement un des 2 champs de paiement a ??t?? saisi, on g??n??re une erreur
            // Sinon si les 2 sont vides, on met ?? jour la facture (cela laisse la possibilit?? de reporter un paiement d'une facture)
            if (
                ($uneFacture->getFkMoyenPaiement() !== null && $uneFacture->getDatePaiement() === null) ||
                ($uneFacture->getFkMoyenPaiement() == null && $uneFacture->getDatePaiement() !== null)
            ) {
                $message = "L'un des champs de paiement a ??t?? saisi, veuillez les remplir ou les laisser vide.";
                return $this->render('facture/ajout.html.twig', [
                    'errors' => $form->addError(new FormError($message))->getErrors(true),
                    'formAjoutFacture' => $form->createView(),
                    'listeInterventions' => $listeInterventions
                ]);
            }
            // On persiste l'objet Facture dans l'entit?? Facture
            $entityManager->persist($uneFacture);
            $entityManager->flush();

            // Equivalent de la fonction lastInsertId() qui permet de r??cup??rer le dernier identifiant ins??r?? dans la table facture
            $idFacture = $uneFacture->getId();

            // R??cup??re la liste des interventions termin??es du client qui ne sont pas factur??es
            $liste = $interventionRepository->findBy(['fk_client' => $uneFacture->getFKClient()->getId(), 'fk_facture' => null, 'fk_etat' => $etatRepository->findOneBy(['etat' => 'Termin??'])->getId()]);

            // Boucle sur chaque intervention pour r??cup??rer l'identifiant de l'intervention du client ?? factur?? puis concat??ne ces identifiants dans un tableau
            $tabIdInterventions = [];
            foreach ($liste as $value){
                array_push($tabIdInterventions, $value->getId());
            }

            // Met ?? jour l'etat des interventions ?? 'Factur??' et associe le dernier n?? facture aux identifiants du tableau ci-dessus
            $interventionRepository->updateInterventionByEtatAndNumFacture($tabIdInterventions, $etatRepository->findOneBy(['etat' => 'Factur??'])->getId(), $idFacture);

            // G??n??re et enregistre le PDF
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
     * @Route("/facture/modifier/{id}", name="facture_modifier", defaults={"id" = 0}, methods={"GET", "POST"})
     */
    public function modifier(int $id, FactureRepository $factureRepository, InterventionRepository $interventionRepository, TVARepository $TVARepository, ClientRepository $clientRepository, Request $request): Response
    {
        $uneFacture = $factureRepository->find($id);
        $listeInterventions = $interventionRepository->findBy(['fk_client' => $uneFacture->getFKClient()->getId(), 'fk_facture' => $uneFacture->getId()]);

        // Si le param??tre est ??gale ?? z??ro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($id == 0 || $uneFacture == null) {
            $this->addFlash('facture', 'Cette facture n\'existe pas.');
            return $this->redirectToRoute('facture_index');
        }

        // R??cup??re les donn??es du client et du taux TVA de la facture
        // Utilis??s apr??s la soumission du formulaire puisque les champs "client" et "facture" sont d??sactiv??s
        $client = $uneFacture->getFkClient();
        $taux = $uneFacture->getFkTaux();

        $form = $this->createForm(ModificationFactureType::class, $uneFacture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Met ?? jour la facture
            $factureRepository->updateFacture($uneFacture);

            // D??finit les valeurs de l'objet Facture avec les variables pr??c??dentes contenant ces informations
            $uneFacture->setFkClient($client);
            $uneFacture->setFkTaux($taux);

            // R??cup??re les interventions de la facture
            $listeInterventions = $interventionRepository->findBy(['fk_facture' => $uneFacture->getId()]);

            // G??n??re le PDF de la facture avec les nouvelles informations
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
     * @Route("/facture/envoyer/{id}", name="facture_envoyer", defaults={"id" = 0}, methods={"GET", "POST"})
     */
    public function envoyer(int $id, FactureRepository $factureRepository, Request $request, MailerInterface $mailer): Response
    {
        $uneFacture = $factureRepository->find($id);

        // Si le param??tre est ??gale ?? z??ro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if ($id == 0 || $uneFacture == null) {
            $this->addFlash('facture', 'Cette facture n\'existe pas.');
            return $this->redirectToRoute('facture_index');
        }

        // V??rifie et r??cup??re le fichier PDF de la facture
        $fichier = $id.'.pdf';
        $chemin = $this->getParameter('kernel.project_dir')."/public";
        $cheminCompletFacture = $chemin."/pdf_facture/".$fichier;

        // Ce formulaire n'est pas reli?? ?? l'entit?? Facture puisqu'il est diff??rent de cette entit?? (expediteur, destinataire...)
        $form = $this->createForm(EnvoiFactureType::class, ["uneFacture" => $uneFacture, "cheminLogo" => $request->getSchemeAndHttpHost()]);
        $form->handleRequest($request);

        // Envoi du mail
        if ($form->isSubmitted() && $form->isValid()) {
            $email = (new Email())
                ->from($form->getData()['expediteur'])
                ->to($form->getData()['destinataire'])
                ->subject($form->getData()['objet'])
                ->html($form->getData()['message']);
            // Si le fichier pour la pi??ce jointe existe, on l'ajoute au mail
            if(file_exists($cheminCompletFacture)) {
                $email->attachFromPath($cheminCompletFacture, "Facture n??".$id.".pdf");
            }

            try {
                $mailer->send($email);
                $this->addFlash('facture_mail_success', 'Le mail a ??t?? envoy??.');
                return $this->redirectToRoute('facture_index');
            } catch (\Exception $t) {
                return new Response($t->getMessage());
            }
        }

        return $this->render('facture/envoi.html.twig', [
            'errors' => $form->getErrors(true),
            'formEnvoiFacture' => $form->createView(),
            "fichier" => file_exists($cheminCompletFacture),
            'uneFacture' => $uneFacture, // Les informations de cette facture seront affich??es avec le template twig
        ]);
    }

    /**
     * @Route("/facture/telecharger/{id}", name="facture_telecharger", defaults={"id" = 0}, methods={"GET"})
     */
    public function telecharger(int $id, FactureRepository $factureRepository, Request $request): Response
    {
        // R??cup??re toutes les infos sur la facture
        $uneFacture = $factureRepository->findOneBy(['id' => $id]);

        // Si le param??tre est ??gale ?? z??ro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($id == 0 || $uneFacture == null) {
            $this->addFlash('facture', 'Cette facture n\'existe pas.');
            return $this->redirectToRoute('facture_index');
        }

        // R??cup??ration puis affichage du PDF de la facture
        $chemin = $this->getParameter('kernel.project_dir')."/public/pdf_facture/".$id.".pdf";
        return $this->file($chemin, null, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    // Fonction qui g??n??re le PDF ?? partir de l'objet Facture et d'un tableau d'interventions
    public function genererPdf(Facture $facture, array $intervention) {
        // R??cup??ration du logo pour le PDF
        $logo = $this->getParameter('kernel.project_dir')."/public/images/logo_64.png";

        // G??n??ration du contenu du PDF
        $html = $this->renderView('facture/donnees_pdf.html.twig', [
            'uneFacture' => $facture,
            'listeInterventions' => $intervention,
            'logo' => $logo
        ]);

        // G??n??ration nom du fichier avec l'emplacement de sauvegarde du PDF sur le disque
        $fichier = $facture->getId().'.pdf';
        $chemin = $this->getParameter('kernel.project_dir')."/public/pdf_facture";
        $cheminComplet = $chemin."/".$fichier;

        // G??n??re le PDF final
        $pdf = new Html2Pdf('P', 'A4', 'fr');
        $pdf->pdf->setTitle("Facture n??".$facture->getId());
        $pdf->writeHTML($html);

        // Enregiste le PDF dans un fichier dans le dossier des factures
        $data = $pdf->pdf->getPDFData();
        file_put_contents($cheminComplet, $data);
    }

    /**
     * @Route("/facture/infos", name="facture_infos", methods={"POST"})
     */
    public function infos(InterventionRepository $interventionRepository, EtatRepository $etatRepository, Request $request)
    {
        // R??cup??re l'identifiant pour la requ??te
        $id = (int) $request->request->get('clientID');
        if (!empty($id) && $id !== 0) {
            // Renvoi la liste des interventions non factur??s des v??hicules du client pour Ajax au format JSON
            $liste = $interventionRepository->findBy(['fk_client' => $id, 'fk_facture' => null, 'fk_etat' => $etatRepository->findOneBy(['etat' => 'Termin??'])->getId()]);
            return $this->json(['donnees' => $liste]);
        }
        else {
            $this->addFlash('facture', 'Cet acc??s est restreint.');
            return $this->redirectToRoute('facture_index');
        }
    }
}
