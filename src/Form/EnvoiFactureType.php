<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnvoiFactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('expediteur', EmailType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Expéditeur :',
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('destinataire', EmailType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Destinataire :',
                'data' => $options['data']['email'],
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('objet', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Objet :',
                'data' => 'Facture n°'.$options['data']['id'],
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'cols' => 50,
                    'rows' => 4,
                ],
                'label' => 'Message :',
                'data' => 'Voici la facture n°'.$options['data']['id'].".",
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
            // Configure your form options here
        ]);
    }
}