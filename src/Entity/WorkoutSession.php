<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\WorkoutSessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['workoutSession:read:item']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['workoutSession:read:collection']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['workoutSession:write']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['workoutSession:write']]
        ),
        new Delete()
    ]
)]
#[ORM\Entity(repositoryClass: WorkoutSessionRepository::class)]
class WorkoutSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'workoutSessions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['workoutSession:read:item', 'workoutSession:write'])]
    private ?WorkoutProgram $program = null;

    #[ORM\ManyToOne(inversedBy: 'workoutSessions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['workoutSession:read:item', 'workoutSession:write'])]
    private ?Trainer $trainer = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "Start time is required.")]
    #[Assert\Type(\DateTimeInterface::class)]
    #[Groups(['workoutSession:read:item', 'workoutSession:write'])]
    private ?\DateTimeInterface $start_time = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "End time is required.")]
    #[Assert\Type(\DateTimeInterface::class)]
    #[Assert\GreaterThan(propertyPath: "start_time", message: "End time must be later than start time.")]
    #[Groups(['workoutSession:read:item', 'workoutSession:write'])]
    private ?\DateTimeInterface $end_time = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'workoutSession', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['workoutSession:read:item'])]
    private Collection $users;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'workoutSession', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['workoutSession:read:item'])]
    private Collection $bookings;

    /**
     * @var Collection<int, Equipment>
     */
    #[ORM\ManyToMany(targetEntity: Equipment::class, inversedBy: 'workoutSessions')]
    #[Groups(['workoutSession:read:item', 'workoutSession:write'])]
    private Collection $equipment;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->equipment = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProgram(): ?WorkoutProgram
    {
        return $this->program;
    }

    public function setProgram(?WorkoutProgram $program): static
    {
        $this->program = $program;

        return $this;
    }

    public function getTrainer(): ?Trainer
    {
        return $this->trainer;
    }

    public function setTrainer(?Trainer $trainer): static
    {
        $this->trainer = $trainer;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->start_time;
    }

    public function setStartTime(\DateTimeInterface $start_time): static
    {
        $this->start_time = $start_time;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->end_time;
    }

    public function setEndTime(\DateTimeInterface $end_time): static
    {
        $this->end_time = $end_time;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setWorkoutSession($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getWorkoutSession() === $this) {
                $user->setWorkoutSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setWorkoutSession($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getWorkoutSession() === $this) {
                $booking->setWorkoutSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Equipment>
     */
    public function getEquipment(): Collection
    {
        return $this->equipment;
    }

    public function addEquipment(Equipment $equipment): static
    {
        if (!$this->equipment->contains($equipment)) {
            $this->equipment->add($equipment);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): static
    {
        $this->equipment->removeElement($equipment);

        return $this;
    }
}
