<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
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
    private $DateMsg;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Object;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Statut;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $UserId;

    /**
     * @ORM\OneToOne(targetEntity=Answer::class, mappedBy="MessageId", cascade={"persist", "remove"})
     */
    private $answer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateMsg(): ?\DateTimeInterface
    {
        return $this->DateMsg;
    }

    public function setDateMsg(\DateTimeInterface $DateMsg): self
    {
        $this->DateMsg = $DateMsg;

        return $this;
    }

    public function getObject(): ?string
    {
        return $this->Object;
    }

    public function setObject(string $Object): self
    {
        $this->Object = $Object;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getStatut(): ?bool
    {
        return $this->Statut;
    }

    public function setStatut(bool $Statut): self
    {
        $this->Statut = $Statut;

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

    public function getAnswer(): ?Answer
    {
        return $this->answer;
    }

    public function setAnswer(?Answer $answer): self
    {
        // unset the owning side of the relation if necessary
        if ($answer === null && $this->answer !== null) {
            $this->answer->setMessageId(null);
        }

        // set the owning side of the relation if necessary
        if ($answer !== null && $answer->getMessageId() !== $this) {
            $answer->setMessageId($this);
        }

        $this->answer = $answer;

        return $this;
    }
}
