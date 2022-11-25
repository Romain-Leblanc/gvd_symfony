<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class KilometrageValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var Kilometrage $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        // Valeur comprise entre 0 et 2 millions de kms
        if($value < 1 || $value > 2000000){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
