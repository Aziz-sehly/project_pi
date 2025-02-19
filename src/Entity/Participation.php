<?php

// namespace App\Entity;

// use App\Repository\ParticipationRepository;
// use Doctrine\ORM\Mapping as ORM;
// use Symfony\Component\Validator\Constraints as Assert;
// use App\Entity\Evenement; // Evenement import, if still needed

// #[ORM\Entity(repositoryClass: ParticipationRepository::class)]
// class Participation
// {
//     #[ORM\Id]
//     #[ORM\GeneratedValue]
//     #[ORM\Column]
//     private ?int $id = null;

//     #[ORM\Column(length: 255)]
//     #[Assert\NotBlank(message: "The user's name is required")]
//     #[Assert\Length(
//         min: 2,
//         max: 50,
//         minMessage: "The name must contain at least {{ limit }} characters",
//         maxMessage: "The name cannot exceed {{ limit }} characters"
//     )]
//     private ?string $utilisateur = null;

//     #[ORM\ManyToOne(targetEntity: Evenement::class, inversedBy: 'participations')]
//     #[Assert\NotNull(message: "You must select an event")]
//     private ?Evenement $evenement = null;

//     #[ORM\Column]
//     #[Assert\NotBlank(message: "The number of seats is required")]
//     #[Assert\Positive(message: "The number of seats must be positive")]
//     #[Assert\LessThan(
//         value: 100,
//         message: "The number of seats cannot exceed {{ value }}"
//     )]
//     private ?int $nombre_places = null;

//     public function getId(): ?int
//     {
//         return $this->id;
//     }

//     public function getUtilisateur(): ?string
//     {
//         return $this->utilisateur;
//     }

//     public function setUtilisateur(string $utilisateur): static
//     {
//         $this->utilisateur = $utilisateur;

//         return $this;
//     }

//     public function getEvenement(): ?Evenement
//     {
//         return $this->evenement;
//     }

//     public function setEvenement(?Evenement $evenement): static
//     {
//         $this->evenement = $evenement;

//         return $this;
//     }

//     public function getNombrePlaces(): ?int
//     {
//         return $this->nombre_places;
//     }

//     public function setNombrePlaces(int $nombre_places): static
//     {
//         $this->nombre_places = $nombre_places;

//         return $this;
//     }
// }
//