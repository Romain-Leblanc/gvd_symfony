<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Kilometrage extends Constraint
{
    public $message_valeur = "Le champ 'Kilométrage' doit être compris entre 1 km et 2 millions.";
    public $message_virgule = "Le champ 'Kilométrage' ne doit pas contenir de virgules";
}
