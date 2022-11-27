<?php

namespace App\Form;

use App\Entity\Carburant;
use App\Entity\Client;
use App\Entity\Intervention;
use App\Entity\Marque;
use App\Entity\Modele;
use App\Entity\Vehicule;
use App\Form\DataTransformer\ModeleDataTransformer;
use App\Repository\ModeleRepository;
use App\Validator\Immatriculation;
use App\Validator\Kilometrage;
use App\Validator\Modele as modele_validator;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\DataTransformerChain;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModificationVehiculeType extends AbstractType
{
    private $transformer;
    private $repo;

    public function __construct(ModeleDataTransformer $transformer, ModeleRepository $entityRepository)
    {
        $this->transformer = $transformer;
        $this->repo = $entityRepository;
    }

/*    private function getSelect2SingleField( FormEvent $event, int $entityId = 0)
    {
        $data = $event->getData();
        $form = $event->getForm();


        $form->add( 'fk_modele', EntityType::class, [
            'required' => false,
            'label' => "TestModif :",
            'mapped' => true,
            'class' => Modele::class,
            'by_reference' => true,
            'expanded' => false,
            'query_builder' => function( EntityRepository $er ) use ( $entityId ) {
                return $er->createQueryBuilder( 'mo' )
                    ->where( 'mo.id = :entityId' )
                    ->setParameter( 'entityId', $entityId );
            },
        ] );
    }

    function onPreSetData( FormEvent $event )
    {
        $data = $event->getData();
        $form = $event->getForm();

        if ( ! $data ) :
            return;
        endif;

        // Series
        if ( $data->getFkModele() ) :
            $series = $data->getFkModele();
            $seriesId = $series ? $series->getid() : 0;
            $this->getSelect2SingleField( $event, $seriesId );
        endif;

    }

    function onPreSubmit( FormEvent $event )
    {
        $data = $event->getData();
        $form = $event->getForm();

        if ( ! $data ) :
            return;
        endif;

        // Series
        if ( array_key_exists( 'series', $data ) ) :
            $seriesId = $data['series'];
            if ( !( $this->seriesRepository->findOneBy( [ 'id' => $seriesId ] ) ) ) :
                $series = new Series();
                $series->setName( $seriesId );
                $this->seriesRepository->addOrUpdate( $series, true );
                $data['series'] = $series->getId();
                $seriesId = $series->getid();
                $event->setData( $data );
            endif;
            $this->getSelect2SingleField($event, $seriesId);
        endif;
    }*/

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fk_client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function(Client $client){
                    return mb_strtoupper($client->getNom())." ".ucfirst($client->getPrenom())." - ".mb_strtoupper($client->getVille());
                },
                'attr' => [
                    'class' => 'form-select select-client'
                ],
                'label' => "Client :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ]
            ])
            ->add('fk_marque', EntityType::class, [
                'class' => Marque::class,
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
                ]
            ])
           ->add('fk_modele', EntityType::class, [
                'class' => Modele::class,
                // Retourne la liste des modèles de la marque du véhicule
                'query_builder' => function(ModeleRepository $modeleRepository) use($builder) {
                    return $modeleRepository->createQueryBuilder('mo')
                        ->select('mo')
                        ->where('mo.fk_marque = :id_marque')
                        ->setParameter(':id_marque', $builder->getData()->getFkMarque()->getId())
                        ->orderBy('mo.id');
                },
                'choice_label' => function(Modele $modele){
                    return mb_strtoupper($modele->getModele());
                },
                'attr' => [
                    'class' => 'form-select text-center',
                ],
                'label' => "Modèle :",
                'label_attr' => [
                    'class' => 'label-select-line col-md-6 col-form-label'
                ],
                'constraints' => [
                    new modele_validator()
                ],
            ]);
        $builder->get('fk_modele')->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use($builder) {
            $new = $this->repo->find($event->getData());
            $event->setData($new);
        });
            $builder->add('fk_carburant', EntityType::class, [
                'class' => Carburant::class,
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
                ],
                'label' => "Année :",
                'label_attr' => [
                    'class' => 'text-center col-md-5 col-form-label'
                ],
                'data' => (int) date('Y')-10,
                'required' => true
            ])
            ->add('kilometrage', TextType::class, [
                'attr' => [
                    'class' => 'form-control text-center input-50',
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

/*        $modifyForm = function ($form, $users) {
            $form->add('fk_modele', EntityType::class, [
                'class' => Modele::class,
                'multiple' => true,
                'expanded' => false,
                'choices' => $users,
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($modifyForm) {
                dd($event->getForm(), $event->getData()->getFkModele());
                $modifyForm($event->getForm(), $event->getData()->getFkModele());
            }
        );

        $userRepo = $this->repo; // constructor injection

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,

            function (FormEvent $event) use ($modifyForm, $userRepo) {

                $userIds = $event->getData()['users'] ?? null;
                dd($userRepo);
                $users = $userIds ? $userRepo->createQueryBuilder('mo')

                    ->where('mo.id IN (:userIds)')->setParameter('userIds', $userIds)

                    ->getQuery()->getResult() : [];
                $modifyForm($event->getForm(), $users);

            }

        );*/
/*        $builder->get('fk_modele')
            ->addModelTransformer($this->transformer)
        ;*/
/*        $builder->addEventListener(
            FormEvents::POST_SUBMIT,

            function(FormEvent $event) {
                $form = $event->getForm();
                $data = $form->getData();
                var_dump($data);
                die("end");

            }

        );*/
    }

/*    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        // Si le véhicule est déjà dans une intervention, on ne peut pas modifier à quel client appartient ce véhicule, ni la marque et le modèle.
        if(!empty($options['intervention'])) {
            $disabled = true;
            $view->children['fk_marque']->vars['attr']['disabled'] = $disabled;
        }
        else {
            $disabled = false;
            $onchange = 'getModeleFromMarque(this.value);';
            $view->children['fk_marque']->vars['attr']['disabled'] = $disabled;
            $view->children['fk_marque']->vars['attr']['onchange'] = $onchange;
        }
        $view->children['fk_client']->vars['attr']['disabled'] = $disabled;
        $view->children['fk_modele']->vars['attr']['disabled'] = $disabled;
        $view->children['fk_modele']->vars['attr']['onchange'] = "console.log(this.value)";
    }*/

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
            'intervention' => Intervention::class, // Ajout de la classe "Intervention" qui sera utilisé par le formulaire
            'modele_repository' => ModeleRepository::class, // A
        ]);
    }

}
