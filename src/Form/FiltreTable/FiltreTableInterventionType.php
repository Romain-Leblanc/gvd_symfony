<?php

namespace App\Form\FiltreTable;

use App\Entity\Client;
use App\Entity\Marque;
use App\Entity\Modele;
use App\Entity\Vehicule;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreTableInterventionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupère la liste des interventions
        $lesInterventions = $options['data'];

        $builder
            ->add('id_intervention', ChoiceType::class, [
                'attr' => [
                    'class' => 'select2-value-100',
                    'onchange' => 'submit();'
                ],
                'placeholder' => '',
                'label' => 'N° inter.',
                'choices' => $lesInterventions,
                'choice_label' => 'id',
                'choice_value' => 'id',
            ])
            ->add('date_intervention', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control form-control-sm',
                    'onchange' => 'submit();'
                ],
                'placeholder' => '',
                'label' => "Date intervention",
            ])
            ->add('vehicule', EntityType::class, [
                'class' => Vehicule::class,
                // Sélection des véhicules en intervention possibles
                'query_builder' => function(EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder("v")
                        ->innerJoin(Modele::class, 'mo', Join::WITH, 'v.fk_modele = mo.id')
                        ->innerJoin(Marque::class, 'ma', Join::WITH, 'v.fk_marque = ma.id')
                        ->groupBy('ma.marque, mo.modele')
                        ;
                },
                'choice_label' => function(Vehicule $vehicule){
                    return mb_strtoupper($vehicule->getFKMarque()->getMarque())." ".ucfirst($vehicule->getFKModele()->getModele());
                },
                'attr' => [
                    'class' => 'select2-value-100',
                    'onchange' => 'submit();'
                ],
                'choice_value' => 'id',
                'placeholder' => '',
                'label' => 'Véhicule'
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function(Client $client) {
                    return mb_strtoupper($client->getNom())." ".ucfirst($client->getPrenom());
                },
                'choice_value' => 'id',
                'attr' => [
                    'class' => 'select2-value-100',
                    'onchange' => 'submit();'
                ],
                'placeholder' => '',
                'label' => 'Client'
            ])
            ->add('montant_ht', NumberType::class, [
                // Affiche ce type en <input type='number'>
                'html5' => true,
                'attr' => [
                    'type' => 'number',
                    'precision' => 3,
                    'scale' => 1,
                    'class' => 'form-control form-control-sm',
                    'min' => 0,
                    'onchange' => 'submit();'
                ],
                'label' => "Montant HT",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
