<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\Turbo\Attribute\Broadcast;
use App\Repository\UserRepository;
use App\Entity\Post;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[Broadcast]
#[UniqueEntity(fields: ['username'], message: 'Cet pseudo est déjà utilisé')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('username', new Assert\Required([
            new Assert\NotBlank(),
            new Assert\Length([
                'min' => 4,
                'maxMessage' => 'Nom d\'utilisateur trop court',
            ]),
            new Assert\Length([
                'max' => 100,
                'maxMessage' => 'Nom d\'utilisateur trop long',
            ]),
        ]));

        $metadata->addPropertyConstraint('email', new Assert\Required([
            new Assert\NotBlank(),
            new Assert\Email(),
        ]));

        $metadata->addGetterConstraint('passwordSafe', new Assert\IsTrue([
            'message' => 'Les mots de passes ne correspondent pas',
        ]));
    }

    public function isPasswordSafe(): bool
    {
        return $this->plainPassword === $this->confirmPassword;
    }

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;
    private ?string $plainPassword = null;
    private ?string $confirmPassword = null;

    #[ORM\Column]
    private ?array $roles = [];

    //relations

    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'user')]
    private Collection $posts;

    #[ORM\OneToMany(targetEntity: Track::class, mappedBy: 'user')]
    private Collection $tracks;

    #[ORM\Column(type: 'boolean')]
    private ?bool $isVerified = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(length: 255)]
    private ?string $avatar = null;

    //functions

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    //identifier that represents this user
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function setConfirmPassword(string $confirmPassword): static
    {
        $this->confirmPassword = $confirmPassword;
        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    //relations functions

    public function getUser(): ?string
    {
        return (string) $this->username;
    }

    public function setUser(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setUser($this);
        }
        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }
        return $this;
    }

    public function getTracks(): Collection
    {
        return $this->tracks;
    }

    public function addTracks(Track $tracks): static
    {
        if (!$this->tracks->contains($tracks)) {
            $this->tracks->add($tracks);
            $tracks->setUser($this);
        }
        return $this;
    }

    public function removeTracks(Track $tracks): static
    {
        if ($this->tracks->removeElement($tracks)) {
            // set the owning side to null (unless already changed)
            if ($tracks->getUser() === $this) {
                $tracks->setUser(null);
            }
        }
        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function isDeleted(): bool
    {
        return $this->isVerified == false ? true : false;
    }

    public function isExpired(): bool
    {
        return $this->isVerified == false ? true : false;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;
        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

}
