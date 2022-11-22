<?php

namespace App\Entity;

use App\Repository\InterventionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InterventionRepository::class)
 */
class Intervention
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
     * @ORM\ManyToOne(targetEntity=Vehicule::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $fk_vehicule;

    /**
     * @ORM\ManyToOne(targetEntity=Facture::class,cascade={"persist"})
     */
    private $fk_facture;

    /**
     * @ORM\ManyToOne(targetEntity=Etat::class,cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $fk_etat;

    /**
     * @ORM\Column(type="date")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="date")
     */
    private $date_intervention;

    /**
     * @ORM\Column(type="smallint")
     */
    private $duree_intervention;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $detail_intervention;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $montant_ht;

    public function __construct()
    {
        $this->date_creation = new \DateTime();
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

    public function getFkVehicule(): ?Vehicule
    {
        return $this->fk_vehicule;
    }

    public function setFkVehicule(?Vehicule $fk_vehicule): self
    {
        $this->fk_vehicule = $fk_vehicule;

        return $this;
    }

    public function getFkFacture(): ?Facture
    {
        return $this->fk_facture;
    }

    public function setFkFacture(?Facture $fk_facture): self
    {
        $this->fk_facture = $fk_facture;

        return $this;
    }

    public function getFkEtat(): ?Etat
    {
        return $this->fk_etat;
    }

    public function setFkEtat(?Etat $fk_etat): self
    {
        $this->fk_etat = $fk_etat;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDateIntervention(): ?\DateTimeInterface
    {
        return $this->date_intervention;
    }

    public function setDateIntervention(\DateTimeInterface $date_intervention): self
    {
        $this->date_intervention = $date_intervention;

        return $this;
    }

    public function getDureeIntervention(): ?int
    {
        return $this->duree_intervention;
    }

    public function setDureeIntervention(int $duree_intervention): self
    {
        $this->duree_intervention = $duree_intervention;

        return $this;
    }

    public function getDetailIntervention(): ?string
    {
        return $this->detail_intervention;
    }

    public function setDetailIntervention(string $detail_intervention): self
    {
        $this->detail_intervention = $detail_intervention;

        return $this;
    }

    public function getMontantHt(): ?float
    {
        return $this->montant_ht;
    }

    public function setMontantHt(?float $montant_ht): self
    {
        $this->montant_ht = $montant_ht;

        return $this;
    }
}
