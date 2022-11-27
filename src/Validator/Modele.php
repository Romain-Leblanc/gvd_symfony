<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Modele extends Constraint
{
    public $message = "Le modèle '{{ modele }}' n'appartient pas à la marque '{{ marque }}'";
}
