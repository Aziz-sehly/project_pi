<?php
namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'utilisateur est obligatoire.")]
    #[Assert\Length(max: 255, maxMessage: "Le nom de l'utilisateur ne peut pas dépasser 255 caractères.")]
    private ?string $utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[Assert\NotNull(message: "L'hôtel est obligatoire.")]
    private ?Hotel $id_hotel = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de check-in est obligatoire.")]
    #[Assert\GreaterThan("today", message: "La date de check-in doit être dans le futur.")]
    private ?\DateTimeInterface $check_in = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de check-out est obligatoire.")]
    #[Assert\Expression(
        "this.getCheckIn() < this.getCheckOut()",
        message: "La date de check-out doit être après la date de check-in."
    )]
    private ?\DateTimeInterface $check_out = null;

    
    #[ORM\Column]
    #[Assert\NotNull(message: "Le nombre de personnes est obligatoire.")]
    #[Assert\Positive(message: "Le nombre de personnes doit être supérieur à zéro.")]
    private ?int $number_of_guest = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La méthode de paiement est obligatoire.")]
    #[Assert\Length(max: 255, maxMessage: "La méthode de paiement ne peut pas dépasser 255 caractères.")]
    private ?string $payement_method = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La demande spéciale est obligatoire.")]
    #[Assert\Length(max: 255, maxMessage: "La demande spéciale ne peut pas dépasser 255 caractères.")]
    private ?string $special_request = null;
    
    
    

    
    
    // Getters and setters...
    public function getId(): ?int { return $this->id; }

    public function getUtilisateur(): ?string { return $this->utilisateur; }
    public function setUtilisateur(string $utilisateur): static {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getIdHotel(): ?Hotel { return $this->id_hotel; }
    public function setIdHotel(?Hotel $id_hotel): static {
        $this->id_hotel = $id_hotel;
        return $this;
    }

    public function getCheckIn(): ?\DateTimeInterface { return $this->check_in; }
    public function setCheckIn(\DateTimeInterface $check_in): static {
        $this->check_in = $check_in;
        return $this;
    }

    public function getCheckOut(): ?\DateTimeInterface { return $this->check_out; }
    public function setCheckOut(\DateTimeInterface $check_out): static {
        $this->check_out = $check_out;
        return $this;
    }

    

    public function getNumberOfGuest(): ?int { return $this->number_of_guest; }
    public function setNumberOfGuest(int $number_of_guest): static {
        $this->number_of_guest = $number_of_guest;
        return $this;
    }

    public function getPayementMethod(): ?string { return $this->payement_method; }
    public function setPayementMethod(string $payement_method): static {
        $this->payement_method = $payement_method;
        return $this;
    }

    public function getSpecialRequest(): ?string { return $this->special_request; }
    public function setSpecialRequest(string $special_request): static {
        $this->special_request = $special_request;
        return $this;
    }
    

}
