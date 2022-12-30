<?php

namespace App\Form\FiltreTable\Admin;

use App\Entity\Marque;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminFiltreTableMarqueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupère le tableau des marques
        $lesMarques = $options['data'];

        $builder
            ->add('id_marque', ChoiceType::class, [
                'attr' => [
                    'class' => 'select2-value-100',
                    'onchange' => 'submit();'
                ],
                'placeholder' => '',
                'choices' => $lesMarques,
                'choice_label' => 'id',
                'choice_value' => 'id',
            ])
            ->add('marque', EntityType::class, [
                'class' => Marque::class,
                // Sélection des véhicules en intervention possibles
                'query_builder' => function(EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder("ma")
                        ->groupBy('ma.marque')
                        ;
                },
                'choice_label' => function(Marque $marque) {
                    return mb_strtoupper($marque->getMarque());
                },
                'choice_value' => 'id',
                'attr' => [
                    'class' => 'select2-value-100',
                    'onchange' => 'submit();'
                ],
                'placeholder' => '',
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
