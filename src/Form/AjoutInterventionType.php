<?php

namespace App\Form;

use App\Entity\Intervention;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjoutInterventionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_creation')
            ->add('date_intervention')
            ->add('duree_intervention')
            ->add('detail_intervention')
            ->add('montant_ht')
            ->add('fk_client')
            ->add('fk_vehicule')
            ->add('fk_facture')
            ->add('fk_etat')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Intervention::class,
        ]);
    }
}
