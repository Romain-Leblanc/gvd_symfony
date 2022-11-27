<?php

namespace App\Entity;

use App\Repository\ModeleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ModeleRepository::class)
 */
class Modele
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Marque::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $fk_marque;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $modele;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): self
    {
        $this->modele = $modele;

        return $this;
    }

    // Utilisé par ModeleSubscriber
    public function __toString()
    {
        return $this->getModele();
    }
}
