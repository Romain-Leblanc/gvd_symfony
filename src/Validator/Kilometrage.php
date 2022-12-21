<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Kilometrage extends Constraint
{
    public $message = "Le champ 'Kilométrage' doit être compris entre 1 km et 2 millions.";
}
