<?php

namespace App\Twig;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class FunctionExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
//            new TwigFunction('remplacerAccents', [$this, 'remplacerAccents']),
//            new TwigFunction('nomPrenomVille', [$this, 'nomPrenomVille']),
            new TwigFunction('adresseComplete', [$this, 'adresseComplete']),
//            new TwigFunction('adresseCompleteFacture', [$this, 'adresseCompleteFacture']),
            new TwigFunction('nomPrenom', [$this, 'nomPrenom']),
            new TwigFunction('numTelEmail', [$this, 'numTelEmail']),
            new TwigFunction('affichagePlusieursValeurs', [$this, 'affichagePlusieursValeurs']),
            new TwigFunction('marqueModele', [$this, 'marqueModele']),
/*            new TwigFunction('marqueModeleFacture', [$this, 'marqueModeleFacture']),
            new TwigFunction('detailInterventionLong', [$this, 'detailInterventionLong']),*/
            new TwigFunction('formatMontantEuros', [$this, 'formatMontantEuros']),
/*            new TwigFunction('formatTotalTVA', [$this, 'formatTotalTVA']),
            new TwigFunction('formatTotalTTC', [$this, 'formatTotalTTC']),
            new TwigFunction('dureeIntervention', [$this, 'dureeIntervention']),
            new TwigFunction('voitureImmatriculation', [$this, 'voitureImmatriculation']),
            new TwigFunction('dateJourAnnee', [$this, 'dateJourAnnee']),
            new TwigFunction('moisToFrench', [$this, 'moisToFrench']),*/
            new TwigFunction('dateEnFrancais', [$this, 'dateEnFrancais'])
//            new TwigFunction('dernierJourMoisDate', [$this, 'dernierJourMoisDate'])
        ];
    }

    // Retourne l'adresse complète
    function adresseComplete(string $adresse, string $suite_adresse = null,string $code_postal,string $ville){
        if(is_null($suite_adresse)) {
            return ucfirst($adresse)." - ".$code_postal." ".mb_strtoupper($ville);
        }
        else {
            return ucfirst($adresse)." ".mb_strtoupper($suite_adresse)." - ".$code_postal." ".mb_strtoupper($ville);
        }
    }

    // Retourne le nom et prénom
    function nomPrenom(string $nom, string $prenom){
        return mb_strtoupper($nom)." ".ucfirst($prenom);
    }

    // Retourne le n° téléphone et l'email
    function numTelEmail(string $tel, string $email){
        if($tel[2] !== " "){
            return $tel[0].$tel[1]." ".$tel[2].$tel[3]." ".$tel[4].$tel[5]." ".$tel[6].$tel[7]." ".$tel[8].$tel[9]."<br><span class='text-primary'>".$email."</span>";
        }
        else {
            return $tel."<br>".$email;
        }
    }

    // Retourne un caractère "s" s'il y la valeur est supérieur à 1.
    function affichagePlusieursValeurs(int $valeur) {
        if($valeur > 1) {
            return "s";
        }
        else {
            return "";
        }
    }

    // Retourne la marque et le modèle
    function marqueModele(string $marque, string $modele){
        return mb_strtoupper($marque." ".$modele);
    }

    // Retourne la montant en euros
    function formatMontantEuros(float $montant){
        return number_format($montant, 2, ',', ' ')." €";
    }

    // Retourne la date fourni en format français
    function dateEnFrancais(DateTime $date){
        return $date->format('d/m/Y');
    }
}
