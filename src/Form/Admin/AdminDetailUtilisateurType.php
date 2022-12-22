<?php

namespace App\Form\Admin;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminDetailUtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if(in_array('ROLE_ADMIN', $options['data']->getRoles())) {
            $data = "Administrateur";
        }
        else {
            $data = "Utilisateur";
        }

        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control input-50',
                    'disabled' => true,
                ],
                'label' => "Email :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('roles', TextType::class, [
                'attr' => [
                    'class' => 'form-control input-50',
                    'disabled' => true,
                ],
                'label' => 'Rôle :',
                'data' => $data,
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
            ])
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'form-control input-50',
                    'disabled' => true,
                ],
                'label' => "Nom :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'required' => true
            ])
            ->add('prenom', TextType::class, [
                'attr' => [
                    'class' => 'form-control input-50',
                    'disabled' => true,
                ],
                'label' => "Prénom :",
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
            'data_class' => Utilisateur::class,
        ]);
    }
}
