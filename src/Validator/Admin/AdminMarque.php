<?php

namespace App\Validator\Admin;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AdminMarque extends Constraint
{
    public $message_lettre = "La marque doit contenir uniquement des lettres.";
    public $message_existe = "Cette marque existe déjà.";
}
