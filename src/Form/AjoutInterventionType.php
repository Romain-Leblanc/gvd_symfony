<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Etat;
use App\Entity\Intervention;
use App\Entity\Vehicule;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjoutInterventionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Initialisation des dates
        $dateCreation = new \DateTime();
        $dateIntervention = new \DateTime();

        // On suppose qu'il n'est pas possible d'ajouter une intervention pour le dimanche, donc on la reporte au lendemain
        // Sinon on ajoute un jour à la date de l'intervention
        if(date_modify(date_modify(new \DateTime(), ' - 2 days'), ' + 1 day')->format("l") == "Sunday") {
            $date = date("Y-m-d", strtotime($dateIntervention->format("Y-m-d"). ' + 2 days'));
        }
        else {
            $date = date("Y-m-d", strtotime($dateIntervention->format("Y-m-d"). ' + 1 day'));
        }
        // On transforme la chaine de date en objet DateTime
        $dateIntervention = new \DateTime($date);

        $builder->add('date_creation', HiddenType::class);
        // Transforme l'objet Date en chaine pour que ça puisse être validé en <input type='hidden'>
        $builder->get('date_creation')->addModelTransformer(new DateTimeToStringTransformer());
        $builder->add('heure_intervention', TimeType::class, [
            'mapped' => false,
            'html5' => true,
            'input_format' => 'H\\a\\ti',
            'data' => $dateCreation,
            'widget' => 'single_text',
            'attr' => [
                'class' => 'form-control input-50-date-heure',
                'min' => $dateCreation->format('h:i')
            ],
        ])
            ->add('date_intervention', DateType::class, [
                'widget' => 'single_text',
                'data' => $dateIntervention,
                'attr' => [
                    'class' => 'form-control input-50-date-heure',
                    'min' => $dateCreation->format('Y-m-d')
                ],
                'label' => "Date intervention :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('fk_client', EntityType::class, [
                'class' => Client::class,
                "placeholder" => "-- CLIENTS --",
                // Retourne la liste des clients qui ont au moins 1 véhicule d'enregistré dans la table "Véhicule"
                'query_builder' => function(EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder("c")
                        ->select("c")
                        ->innerJoin(Vehicule::class, 'v', Join::WITH, 'v.fk_client = c.id')
                        ->groupBy("c.id")
                        ->distinct()
                        ;
                },
                'choice_label' => function(Client $client){
                    return mb_strtoupper($client->getNom())." ".ucfirst($client->getPrenom())." - ".mb_strtoupper($client->getVille());
                },
                'attr' => [
                    'class' => 'form-select',
                    'onchange' => 'getInfosFromClientIntervention();'
                ],
                'label' => "Client :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('fk_vehicule', EntityType::class, [
                'class' => Vehicule::class,
                "placeholder" => "-- VEHICULE --",
                'choice_label' => function(Vehicule $vehicule){
                    return mb_strtoupper($vehicule->getFKMarque()->getMarque())." ".ucfirst($vehicule->getFKModele()->getModele())." (".mb_strtoupper($vehicule->getImmatriculation()).")";
                },
                'attr' => [
                    'class' => 'form-select text-center',
                    // Actualisé par Ajax
                    'disabled' => true
                ],
                'label' => "Véhicule :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('fk_etat', EntityType::class, [
                'class' => Etat::class,
                // Sélection de l'état par défaut "en attente" puisqu'on ajoute simplement une intervention
                'query_builder' => function(EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder("e")
                        ->select("e")
                        ->where("e.etat LIKE :etat")
                        ->setParameter(':etat', '%attente%')
                        ;
                },
                'choice_label' => function(Etat $etat){
                    return ucfirst($etat->getEtat());
                },
                'attr' => [
                    'class' => 'form-select input-50',
                    // Actualisé par Ajax
                    'disabled' => true
                ],
                'label' => "État :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('detail_intervention', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 10,
                    'cols' => 50,
                    // Actualisé par Ajax
                    'disabled' => true
                ],
                'label' => "Détail intervention :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            // Durée d'intervention en heure
            ->add('duree_intervention', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control input-50',
                    'min' => 1,
                    'max' => 50,
                    'value' => 1,
                    // Actualisé par Ajax
                    'disabled' => true
                ],
                'label' => "Durée (en heures) :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('montant_ht', NumberType::class, [
                // Affiche ce type en <input type='number'>
                'html5' => true,
                'attr' => [
                    'type' => 'number',
                    'precision' => 3,
                    'scale' => 1,
                    'class' => 'form-control input-50',
                    'value' => 0,
                    // Actualisé par Ajax
                    'disabled' => true
                ],
                'label' => "Montant HT (en €) :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            // Le n° facture sera attribuée à la création de la facture
            ->add('fk_facture', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Intervention::class,
        ]);
    }
}
