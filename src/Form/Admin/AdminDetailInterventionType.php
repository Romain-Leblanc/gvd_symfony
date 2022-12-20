<?php

namespace App\Form\Admin;

use App\Entity\Client;
use App\Entity\Etat;
use App\Entity\Facture;
use App\Entity\Intervention;
use App\Entity\Vehicule;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminDetailInterventionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if(!is_null($options['data']->getFkFacture())) {
            $facture = $options['data']->getFkFacture()->getId();
        }
        else {
            $facture = "Aucune";
        }

        $builder
            ->add('date_intervention', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control input-50',
                    'disabled' => true,
                ],
                'label' => "Date intervention :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('fk_client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function(Client $client){
                    return mb_strtoupper($client->getNom())." ".ucfirst($client->getPrenom())." - ".mb_strtoupper($client->getVille());
                },
                'attr' => [
                    'class' => 'form-control input-50',
                    'disabled' => true,
                ],
                'label' => "Client :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('fk_vehicule', EntityType::class, [
                'class' => Vehicule::class,
                'choice_label' => function(Vehicule $vehicule){
                    return mb_strtoupper($vehicule->getFKMarque()->getMarque())." ".ucfirst($vehicule->getFKModele()->getModele())." (".mb_strtoupper($vehicule->getImmatriculation()).")";
                },
                'attr' => [
                    'class' => 'form-control input-50',
                    'disabled' => true,
                ],
                'label' => "Véhicule :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('fk_etat', EntityType::class, [
                'class' => Etat::class,
                'choice_label' => function(Etat $etat){
                    return ucfirst($etat->getEtat());
                },
                'attr' => [
                    'class' => 'form-control input-50',
                    'disabled' => true,
                ],
                'label' => "État :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('detail_intervention', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control input-50',
                    'rows' => 10,
                    'cols' => 50,
                    'disabled' => true,
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
                    'disabled' => true,
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
                    'class' => 'form-control input-50',
                    'min' => 0,
                    'disabled' => true,
                ],
                'label' => "Montant HT (en €) :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('fk_etat', EntityType::class, [
                'class' => Etat::class,
                'choice_label' => function(Etat $etat){
                    return ucfirst($etat->getEtat());
                },
                'attr' => [
                    'class' => 'form-control input-50',
                    'disabled' => true,
                ],
                'label' => "État :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('fk_facture', TextType::class, [
                'data' => $facture,
                'attr' => [
                    'class' => 'form-control input-50',
                    'disabled' => true,
                ],
                'label' => "Facture associée :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Intervention::class,
        ]);
    }
}
