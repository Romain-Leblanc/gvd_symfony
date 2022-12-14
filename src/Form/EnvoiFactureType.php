<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EnvoiFactureType extends AbstractType
{
    private $token;

    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $uneFacture = $options['data']['uneFacture'];
        // Définit la couleur du texte du champ "piece-jointe" suivant si le fichier PDF existe
        if($options['data']['fichier'] === true) {
            $color = "color: green;";
        }
        else {
            $color = "color: #BE1E2D;";
        }
        $message = "Bonjour ".mb_strtoupper($uneFacture->getFkClient()->getNom())." ".ucfirst($uneFacture->getFkClient()->getPrenom()).",\n\n";
        $message .= "Vous trouverez en pièce jointe la facture n°".$uneFacture->getId().".";
        $message .= "\n\nCordialement,\n";
        $message .= mb_strtoupper($this->token->getToken()->getUser()->getNom())." ".ucfirst($this->token->getToken()->getUser()->getPrenom());
        $message .= "\nGarage Vendelais (G.V.D)";

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
                'data' => $uneFacture->getFkClient()->getEmail(),
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
                'data' => 'Facture n°'.$uneFacture->getId(),
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('piece_jointe', TextType::class, [
                'attr' => [
                    'class' => 'form-control-plaintext',
                    'style' => $color
                ],
                'label' => 'P.J :',
                'data' => $uneFacture->getId().".pdf",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'cols' => 50,
                    'rows' => 10,
                ],
                'label' => 'Message :',
                'data' => $message,
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