<?php

namespace App\Form\FiltreTable;

use App\Entity\Client;
use App\Entity\Facture;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreTableFactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupère la liste des factures
        $lesFactures = $options['data'];

        $builder
            ->add('id_facture', ChoiceType::class, [
                'attr' => [
                    'class' => 'select2-value-100',
                    'onchange' => 'submit();'
                ],
                'placeholder' => '',
                'label' => 'N° facture',
                'choices' => $lesFactures,
                'choice_label' => 'id',
                'choice_value' => 'id',
            ])
            ->add('date_facture', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control form-control-sm',
                    'onchange' => 'submit();'
                ],
                'placeholder' => '',
                'label' => "Date facture",
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function(Client $client) {
                    return mb_strtoupper($client->getNom())." ".ucfirst($client->getPrenom());
                },
                // Sélection des clients de véhicules possibles
                'query_builder' => function(EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('c')
                        ->innerJoin(Facture::class, 'f', Join::WITH, 'f.fk_client = c.id')
                        ;
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
