<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\GroupeRepository;
use App\State\GroupeProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GroupeRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/groupes',
            normalizationContext: ['groups' => 'groupes.read']
        ),
        new GetCollection(
            uriTemplate: '/users_groups',
            normalizationContext: ['groups' => 'users_groupes.read']
        ),
        new Post(
            uriTemplate: '/create-groupe',
            denormalizationContext: array('groups' => 'groupe.write'),
            security: "is_granted('ROLE_ADMIN')",
            processor: GroupeProcessor::class
        ),
        new Delete(
            uriTemplate: '/delete-groupe/{id}',
            security: "is_granted('ROLE_ADMIN')",
        ),
        new Patch(
            uriTemplate: '/edit-groupe/{id}',
            denormalizationContext: array('groups' => 'groupe.edit'),
            security: "is_granted('ROLE_ADMIN')",
            processor: GroupeProcessor::class
        ),
        new Patch(
            uriTemplate: '/edit-users-groupe/{id}',
            denormalizationContext: array('groups' => 'users.groupe.edit'),
            security: "is_granted('ROLE_ADMIN')",
            processor: GroupeProcessor::class
        )
    ]
)]
class Groupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['groupes.read', 'users_groupes.read','user.read','user.profile.me','groupe.write','groupe.edit'])]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'groupe', targetEntity: User::class)]
    #[Groups(['users_groupes.read','users.groupe.edit'])]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setGroupe($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getGroupe() === $this) {
                $user->setGroupe(null);
            }
        }

        return $this;
    }
}
