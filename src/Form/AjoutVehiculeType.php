<?php

namespace App\Form;

use App\Entity\Carburant;
use App\Entity\Client;
use App\Entity\Marque;
use App\Entity\Modele;
use App\Entity\Vehicule;
use App\Validator\Immatriculation;
use App\Validator\Kilometrage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjoutVehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fk_client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function(Client $client){
                    return mb_strtoupper($client->getNom())." ".ucfirst($client->getPrenom())." - ".mb_strtoupper($client->getVille());
                },
                'attr' => [
                    'class' => 'form-select select-client',
                ],
                'label' => "Client :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true,
            ])
            ->add('fk_marque', EntityType::class, [
                'class' => Marque::class,
                "placeholder" => "-- Marque --",
                'choice_label' => function(Marque $marque){
                    return mb_strtoupper($marque->getMarque());
                },
                'attr' => [
                    'class' => 'form-select text-center',
                    'onchange' => 'getModeleFromMarque(this.value);'
                ],
                'label' => "Marque :",
                'label_attr' => [
                    'class' => 'label-select-line col-md-6 col-form-label'
                ],
                'required' => true,
            ])
            ->add('fk_modele', EntityType::class, [
                'class' => Modele::class,
                "placeholder" => "-- Modèle --",
                'choice_label' => function(Modele $modele){
                    return mb_strtoupper($modele->getModele());
                },
                'attr' => [
                    'class' => 'form-select text-center',
                    // Actualisé par Ajax
                    'disabled' => true
                ],
                'label' => "Modèle :",
                'label_attr' => [
                    'class' => 'label-select-line col-md-6 col-form-label'
                ],
                'required' => true,
            ])
            ->add('fk_carburant', EntityType::class, [
                'class' => Carburant::class,
                "placeholder" => "-- Carburant --",
                'choice_label' => function(Carburant $carburant){
                    return mb_strtoupper($carburant->getCarburant());
                },
                'attr' => [
                    'class' => 'form-select text-center input-50',
                ],
                'label' => "Carburant :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ]
            ])
            ->add('annee', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control  input-50',
                    'min' => (int) date('Y') - 75,
                    'max' => (int) date('Y'),
                    'placeholder' => ((int) date('Y') - 75)." à ".((int) date('Y')),
                ],
                'label' => "Année :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('kilometrage', TextType::class, [
                'attr' => [
                    'class' => 'form-control text-center input-50',
                    'placeholder' => 'km'
                ],
                'label' => "Kilométrage :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true,
                'constraints' => [
                    new Kilometrage()
                ]
            ])
            ->add('immatriculation', TextType::class, [
                'attr' => [
                    'class' => 'form-control text-center input-50',
                    'placeholder' => 'AA-123-AA'
                ],
                'label' => "Immatriculation :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true,
                'constraints' => [
                    new Immatriculation()
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}
