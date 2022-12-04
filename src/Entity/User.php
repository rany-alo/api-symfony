<?php

namespace App\Entity;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\ApiResource;
use App\State\UserProcessor;
use App\State\UserStateProvider;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: array(
        new GetCollection(
            uriTemplate: '/users',
            normalizationContext: array('groups' => 'users.read')
        ),
        new Get(
            uriTemplate: '/user/{id}',
            normalizationContext: array('groups' => 'user.read')
        ),
        new Post(
            uriTemplate: '/inscription',
            denormalizationContext: array('groups' => 'user.write'),
            processor: UserProcessor::class
        ),
        new Get(
            uriTemplate: '/profile/me',
            normalizationContext: array('groups' => 'user.profile.me'),
            security: "object == user",
            provider: UserStateProvider::class
        ),
        new Patch(
            uriTemplate: '/userProfileEdit',
            denormalizationContext: array('groups' => 'user.profile.edit'),
            security: "(is_granted('ROLE_USER') and object == user) or (is_granted('ROLE_ADMIN') and object == user)",
            provider: UserStateProvider::class,
            processor: UserProcessor::class
        ),
        new Patch(
            uriTemplate: '/userGroupeEdit',
            denormalizationContext: array('groups' => 'user.groupe.edit'),
            security: "(is_granted('ROLE_USER') and object == user) or (is_granted('ROLE_ADMIN') and object == user)",
            provider: UserStateProvider::class,
            processor: UserProcessor::class
        ),
        new Patch(
            uriTemplate: '/profileEdit/{id}',
            denormalizationContext: array('groups' => 'users.profile.edit'),
            security: "is_granted('ROLE_ADMIN')",
            processor: UserProcessor::class
        ),
        new Delete(
            uriTemplate: '/delete-user/{id}',
            security: "is_granted('ROLE_ADMIN')",
        )
    )
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user.read','user.write','user.profile.me','users.profile.edit','user.profile.edit'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['users.profile.edit'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Groups(['users.read','users_groupes.read','user.read','user.write','user.profile.me',
        'users.profile.edit','user.profile.edit'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['users.read','users_groupes.read','user.read','user.write','user.profile.me',
        'users.profile.edit','user.profile.edit'])]
    private ?string $lastname = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['user.read','user.write','user.profile.me','users.profile.edit','user.groupe.edit'])]
    private ?Groupe $groupe = null;

    #[Groups(['user.write','users.profile.edit','user.profile.edit','user.profile.edit'])]
    #[SerializedName('password')]
    private ?string $plainPassword = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getGroupe(): ?Groupe
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupe $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }
}
