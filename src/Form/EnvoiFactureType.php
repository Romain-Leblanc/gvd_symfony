<?php

namespace App\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
        // Génère le contenu du mail
        $message = "Bonjour ".mb_strtoupper($uneFacture->getFkClient()->getNom())." ".ucfirst($uneFacture->getFkClient()->getPrenom()).",<br><br>";
        $message .= "Vous trouverez en pièce jointe la facture n°".$uneFacture->getId().".";
        $message .= "<br><br>Cordialement,<br>";
        $message .= mb_strtoupper($this->token->getToken()->getUser()->getNom())." ".ucfirst($this->token->getToken()->getUser()->getPrenom());
        $message .= "<br>Garage Vendelais";
        $message .= "<br><br><img src='".$options['data']['cheminLogo']."/images/logo_64.png' alt='logo'>";

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
            ->add('message', CKEditorType::class, [
                'config' => [
                    'height' => 300,
                    'resize_enabled' => false
                ],
                'attr' => [
                    'class' => 'form-control'
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