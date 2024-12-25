<?php

namespace App\Entity;

use App\Repository\FeedbackRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;

#[ApiResource(
    collectionOperations: [
        'get' => [
            'method' => 'GET',
            'normalization_context' => ['groups' => ['get:collection:feedback']],
        ],
        'post' => [
            'method' => 'POST',
            'denormalization_context' => ['groups' => ['post:collection:feedback']],
            'normalization_context' => ['groups' => ['get:collection:feedback']],
        ]
    ],
    itemOperations: [
        'get' => [
            'method' => 'GET',
            'normalization_context' => ['groups' => ['get:item:feedback']],
        ],
        'put' => [
            'method' => 'PUT',
            'denormalization_context' => ['groups' => ['put:item:feedback']],
            'normalization_context' => ['groups' => ['get:item:feedback']],
        ],
        'delete' => [
            'method' => 'DELETE',
        ]
    ]
)]
#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
class Feedback
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'feedback')]
    #[Assert\NotNull(message: "User must be specified.")]
    #[Groups(['get:collection:feedback', 'get:item:feedback', 'post:collection:feedback'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'feedback')]
    #[Assert\NotNull(message: "Trainer must be specified.")]
    #[Groups(['get:collection:feedback', 'get:item:feedback', 'post:collection:feedback'])]
    private ?Trainer $trainer = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull(message: "Rating is required.")]
    #[Assert\Range(
        min: 1,
        max: 5,
        notInRangeMessage: "Rating must be between {{ min }} and {{ max }}."
    )]
    #[Groups(['get:collection:feedback', 'get:item:feedback', 'post:collection:feedback'])]
    private ?int $rating = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Comment cannot be blank.")]
    #[Assert\Length(
        min: 10,
        minMessage: "Comment must be at least {{ limit }} characters long."
    )]
    #[Groups(['get:collection:feedback', 'get:item:feedback', 'post:collection:feedback'])]
    private ?string $comment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
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

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;
        return $this;
    }
}
