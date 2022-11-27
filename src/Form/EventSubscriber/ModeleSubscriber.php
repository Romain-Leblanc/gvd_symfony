<?php

namespace App\Form\EventSubscriber;

use App\Entity\Modele;
use App\Repository\ModeleRepository;
use App\Validator\Modele as modele_validator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ModeleSubscriber implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        );
    }

    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        //tag is not mandatory
        if (!$data['fk_modele']) {
            return;
        }

        // Regénère l'EntityType après la validation
        $form->add('fk_modele', EntityType::class, [
           'class' => Modele::class,
           'query_builder' => function(ModeleRepository $modeleRepository) use ($data) {
               return $modeleRepository->createQueryBuilder('mo')
                   ->select('mo')
                   ->where('mo.fk_marque = :id_marque')
                   ->setParameter(':id_marque', $data['fk_marque'])
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
            'required' => true,
           'constraints' => [
               new modele_validator()
           ],
        ]);
    }
}