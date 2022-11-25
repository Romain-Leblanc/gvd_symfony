<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CodePostal extends Constraint
{
    public $message = 'Le champ du code postal doit être composé uniquement de 5 chiffres.';
}
