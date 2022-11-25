<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Kilometrage extends Constraint
{
    public $message = "Le champ 'Kilométrage' doit avoir une longueur entre 1 et 2 millions de kms.";
}
