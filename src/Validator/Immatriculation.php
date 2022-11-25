<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Immatriculation extends Constraint
{
    public $message = "Immatriculation '{{ value }}' incorrecte.\n\rFormats acceptés : AA-123-AA ou 1234-AA-12";
}
