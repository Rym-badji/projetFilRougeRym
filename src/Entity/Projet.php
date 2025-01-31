<?php

namespace App\Entity;

use App\Repository\ProjetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjetRepository::class)]
class Projet
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

    private ?string $status = null;

    // 
    // vu projet privatiser pour les users relié au projet
    
    /**
     * @var Collection<int, user>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'projets')]
    private Collection $user;

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user; // Retourne la liste des utilisateurs associés au projet
    }

    // fin partiel privatisation

    /**
     * @var Collection<int, Task>
     */
    // Rajout OrphanRemoval pour être sûr que les tâches soient supprimées si le projet est supprimé
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'projet', orphanRemoval: true)]
    private Collection $tasks;

    #[ORM\Column(type: 'boolean')]
    private bool $isManuallyTerminated = false;

    public function __toString()
    {
        return $this->title . " " . $this->content . " " . $this->startDate . " " . $this->endDate;
    }

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->tasks = new ArrayCollection();
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

    // Ajout status

    public function markAsTerminated(): void
    {
        $this->isManuallyTerminated = true;
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

    // fin ajout status

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->user->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setProjet($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getProjet() === $this) {
                $task->setProjet(null);
            }
        }

        return $this;
    }
}
