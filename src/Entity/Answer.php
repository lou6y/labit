<?php

namespace App\Entity;

use App\Repository\AnswerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AnswerRepository::class)
 */
class Answer
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
    private $DateAns;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Description;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="answers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $UserId;

    /**
     * @ORM\OneToOne(targetEntity=Message::class, inversedBy="answer", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $MessageId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAns(): ?\DateTimeInterface
    {
        return $this->DateAns;
    }

    public function setDateAns(\DateTimeInterface $DateAns): self
    {
        $this->DateAns = $DateAns;

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

    public function getUserId(): ?User
    {
        return $this->UserId;
    }

    public function setUserId(?User $UserId): self
    {
        $this->UserId = $UserId;

        return $this;
    }

    public function getMessageId(): ?Message
    {
        return $this->MessageId;
    }

    public function setMessageId(?Message $MessageId): self
    {
        $this->MessageId = $MessageId;

        return $this;
    }
}
