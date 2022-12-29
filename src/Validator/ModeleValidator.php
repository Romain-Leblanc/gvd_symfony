<?php

namespace App\Validator;

use App\Repository\ModeleRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ModeleValidator extends ConstraintValidator
{
    private $repository;

    public function __construct(ModeleRepository $modeleRepository)
    {
        $this->repository = $modeleRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var Modele $constraint */

        if (null === $value || '' === $value) {
            return $this->context->buildViolation("Veuillez sélectionner un modèle de la marque.")
                ->addViolation();
        }

        // Si aucun modèle n'est associé à l'identifiant de la marque, on génère une erreur
        if(empty($this->repository->findBy(['fk_marque' => $value->getFkMarque()->getId(), 'modele' => $value->getModele()]))) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ marque }}', $value->getFkMarque()->getMarque())
                ->setParameter('{{ modele }}', $value->getModele())
                ->addViolation();
        }
    }
}
