<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $DateCmd;

    /**
     * @ORM\Column(type="integer")
     */
    private $PrixTotale;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commandes")
     */
    private $UserId;

    /**
     * @ORM\ManyToMany(targetEntity=Produit::class, inversedBy="commandes")
     */
    private $ProduitId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Observation;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Confirmed;

    public function __construct()
    {
        $this->ProduitId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCmd(): ?\DateTimeInterface
    {
        return $this->DateCmd;
    }

    public function setDateCmd(\DateTimeInterface $DateCmd): self
    {
        $this->DateCmd = $DateCmd;

        return $this;
    }

    public function getPrixTotale(): ?int
    {
        return $this->PrixTotale;
    }

    public function setPrixTotale(int $PrixTotale): self
    {
        $this->PrixTotale = $PrixTotale;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->UserId;
    }

    public function setUserId(?User $UserId): self
    {
        $this->UserId = $UserId;

        return $this;
    }

    /**
     * @return Collection|Produit[]
     */
    public function getProduitId(): Collection
    {
        return $this->ProduitId;
    }

    public function addProduitId(Produit $produitId): self
    {
        if (!$this->ProduitId->contains($produitId)) {
            $this->ProduitId[] = $produitId;
        }

        return $this;
    }

    public function removeProduitId(Produit $produitId): self
    {
        $this->ProduitId->removeElement($produitId);

        return $this;
    }

    public function getObservation(): ?string
    {
        return $this->Observation;
    }

    public function setObservation(?string $Observation): self
    {
        $this->Observation = $Observation;

        return $this;
    }

    public function getConfirmed(): ?bool
    {
        return $this->Confirmed;
    }

    public function setConfirmed(bool $Confirmed): self
    {
        $this->Confirmed = $Confirmed;

        return $this;
    }
}
