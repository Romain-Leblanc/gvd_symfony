<?php

namespace App\Validator\Admin;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AdminModele extends Constraint
{
    public $message = "Ce modèle de cette marque existe déjà.";
}
