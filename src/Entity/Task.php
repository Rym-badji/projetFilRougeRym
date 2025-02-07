<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\SecurityBundle\Security;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?Projet $projet = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isManuallyTerminated = false;

    // ajout date de fin réelle
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $realEndDate = null;

    public function __toString()
    {
        return $this->title . " " . $this->content . " " . $this->startDate . " " . $this->endDate;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getProjet(): ?Projet
    {
        return $this->projet;
    }

    public function setProjet(?Projet $projet): static
    {
        $this->projet = $projet;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        // Assurer que l'utilisateur connaît ses tâches
        if ($user !== null && !$user->getTasks()->contains($this)) {
            $user->addTask($this);
        }

        return $this;
    }

    // Ajout status

    public function isManuallyTerminated(): bool
    {
        return $this->isManuallyTerminated;
    }

    public function setManuallyTerminated(bool $isManuallyTerminated): self
    {
        $this->isManuallyTerminated = $isManuallyTerminated;
        return $this;
    }

    public function calculateStatus(): string
    {
        if ($this->isManuallyTerminated) {
            return 'Terminé';
        }
        
        $now = new \DateTime();
        if ($this->endDate < $now) {
            return 'Terminé automatiquement';
        } elseif ($this->startDate > $now) {
            return 'En attente';
        } else {
            return 'En cours';
        }
    }
    
    public function getStatus(): string
    {
        return $this->calculateStatus();
    }

    public function markAsTerminated(): void
    {
        $this->isManuallyTerminated = true;
    }

    // fin ajout status
    // ajout date de fin réelle
 
    public function getRealEndDate(): ?\DateTimeInterface
    {
        return $this->realEndDate;
    }

    public function setRealEndDate(?\DateTimeInterface $realEndDate): self
    {
        $this->realEndDate = $realEndDate;
        return $this;
    }
}
