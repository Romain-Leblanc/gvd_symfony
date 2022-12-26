<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FactureRepository::class)
 */
class Facture
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $fk_client;

    /**
     * @ORM\ManyToOne(targetEntity=TVA::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $fk_taux;

    /**
     * @ORM\ManyToOne(targetEntity=MoyenPaiement::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $fk_moyen_paiement;

    /**
     * @ORM\Column(type="date")
     */
    private $date_facture;

    /**
     * @ORM\Column(type="date",nullable=true)
     */
    private $date_paiement;

    /**
     * @ORM\Column(type="float")
     */
    private $montant_ht;

    /**
     * @ORM\Column(type="float")
     */
    private $montant_tva;

    /**
     * @ORM\Column(type="float")
     */
    private $montant_ttc;

    public function __construct()
    {
        $this->date_facture = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFkClient(): ?Client
    {
        return $this->fk_client;
    }

    public function setFkClient(?Client $fk_client): self
    {
        $this->fk_client = $fk_client;

        return $this;
    }

    public function getFkTaux(): ?TVA
    {
        return $this->fk_taux;
    }

    public function setFkTaux(?TVA $fk_taux): self
    {
        $this->fk_taux = $fk_taux;

        return $this;
    }

    public function getFkMoyenPaiement(): ?MoyenPaiement
    {
        return $this->fk_moyen_paiement;
    }

    public function setFkMoyenPaiement(?MoyenPaiement $fk_moyen_paiement): self
    {
        $this->fk_moyen_paiement = $fk_moyen_paiement;

        return $this;
    }

    public function getDateFacture(): ?\DateTimeInterface
    {
        return $this->date_facture;
    }

    public function setDateFacture(\DateTimeInterface $date_facture): self
    {
        $this->date_facture = $date_facture;

        return $this;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->date_paiement;
    }

    public function setDatePaiement(?\DateTimeInterface $date_paiement): self
    {
        $this->date_paiement = $date_paiement;

        return $this;
    }

    public function getMontantHt(): ?float
    {
        return $this->montant_ht;
    }

    public function setMontantHt(float $montant_ht): self
    {
        $this->montant_ht = $montant_ht;

        return $this;
    }

    public function getMontantTva(): ?float
    {
        return $this->montant_tva;
    }

    public function setMontantTva(float $montant_tva): self
    {
        $this->montant_tva = $montant_tva;

        return $this;
    }

    public function getMontantTtc(): ?float
    {
        return $this->montant_ttc;
    }

    public function setMontantTtc(float $montant_ttc): self
    {
        $this->montant_ttc = $montant_ttc;

        return $this;
    }
}
