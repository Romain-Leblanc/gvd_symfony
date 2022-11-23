<?php

namespace App\Entity;

use App\Repository\VehiculeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VehiculeRepository::class)
 */
class Vehicule
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
     * @ORM\ManyToOne(targetEntity=Marque::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $fk_marque;

    /**
     * @ORM\ManyToOne(targetEntity=Modele::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $fk_modele;

    /**
     * @ORM\ManyToOne(targetEntity=Carburant::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $fk_carburant;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $immatriculation;

    /**
     * @ORM\Column(type="bigint")
     */
    private $kilometrage;

    /**
     * @ORM\Column(type="integer")
     */
    private $annee;

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

    public function getFkMarque(): ?Marque
    {
        return $this->fk_marque;
    }

    public function setFkMarque(?Marque $fk_marque): self
    {
        $this->fk_marque = $fk_marque;

        return $this;
    }

    public function getFkModele(): ?Modele
    {
        return $this->fk_modele;
    }

    public function setFkModele(?Modele $fk_modele): self
    {
        $this->fk_modele = $fk_modele;

        return $this;
    }

    public function getFkCarburant(): ?Carburant
    {
        return $this->fk_carburant;
    }

    public function setFkCarburant(?Carburant $fk_carburant): self
    {
        $this->fk_carburant = $fk_carburant;

        return $this;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(string $immatriculation): self
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }

    public function getKilometrage(): ?string
    {
        return $this->kilometrage;
    }

    public function setKilometrage(string $kilometrage): self
    {
        $this->kilometrage = $kilometrage;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): self
    {
        $this->annee = $annee;

        return $this;
    }
}
