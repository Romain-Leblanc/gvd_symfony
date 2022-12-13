<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Etat;
use App\Entity\Facture;
use App\Entity\Intervention;
use App\Entity\MoyenPaiement;
use App\Entity\TVA;
use App\Repository\TVARepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjoutFactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $dateFacture = new \DateTime();

        $builder
            ->add('date_facture', DateType::class, [
                'widget' => 'single_text',
                'data' => $dateFacture,
                'attr' => [
                    'class' => 'form-control input-50',
                    'min' => $dateFacture->format('Y-m-d')
                ],
                'label' => "Date facture :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('fk_client', EntityType::class, [
                'class' => Client::class,
                "placeholder" => "-- CLIENTS --",
                // Retourne la liste des clients qui ont au moins 1 intervention terminée non facturé
                'query_builder' => function(EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder("c")
                        ->select("c")
                        ->innerJoin(Intervention::class, 'i', Join::WITH, 'i.fk_client = c.id')
                        ->innerJoin(Etat::class, 'e', Join::WITH, 'i.fk_etat = e.id')
                        ->where("e.etat = :etat")
                        ->setParameter('etat', 'Terminé')
                        ->andWhere("i.fk_facture IS NULL")
                    ;
                },
                'choice_label' => function(Client $client){
                    return mb_strtoupper($client->getNom())." ".ucfirst($client->getPrenom())." - ".mb_strtoupper($client->getVille());
                },
                'attr' => [
                    'class' => 'form-select',
                    'onchange' => 'getInfosFromClientFacture();'
                ],
                'label' => "Client :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('fk_taux', EntityType::class, [
                'class' => TVA::class,
                'choice_label' => function(TVA $taux){
                    return str_replace(".", ",", $taux->getTaux())." %";
                },
                'attr' => [
                    'class' => 'form-select input-50',
                    'onchange' => 'changeTotalFromTaux();',
                    'disabled' => true,
                ],
                'label' => "Taux :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('fk_moyen_paiement', EntityType::class, [
                'class' => MoyenPaiement::class,
                'choice_label' => function(MoyenPaiement $moyenPaiement){
                    return $moyenPaiement->getMoyenPaiement();
                },
                'attr' => [
                    'class' => 'form-select',
                    'disabled' => true,
                ],
                'label' => "Moyen paiement :",
                'label_attr' => [
                    'class' => 'text-center col-md-6 col-form-label'
                ],
                'required' => true
            ])
            ->add('date_paiement', DateType::class, [
                'widget' => 'single_text',
                'data' => $dateFacture,
                'attr' => [
                    'class' => 'form-control',
                    'min' => $dateFacture->format('Y-m-d'),
                    'disabled' => true,
                ],
                'label' => "Date paiement :",
                'label_attr' => [
                    'class' => 'text-center col-md-6 col-form-label'
                ],
                'required' => true
            ])
            ->add('montant_ht', HiddenType::class, [
                'data' => '0,00 €'
            ])
            ->add('montant_tva', HiddenType::class, [
                'data' => '0,00 €'
            ])
            ->add('montant_ttc', HiddenType::class, [
                'data' => '0,00 €'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);
    }
}
