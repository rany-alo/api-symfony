<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProcessor implements ProcessorInterface
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher,
                                private readonly EntityManagerInterface $entityManager)
    {}
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if(false === $data instanceof User) {
            return;
        }
        if($operation->getName() === "post") {
            $data->setUpdatedAt(new \DateTimeImmutable());
        }
        $data->setPassword($this->userPasswordHasher->hashPassword($data, $data->getPlainPassword()));
        $data->setCreatedAt(new \DateTimeImmutable());
        $data->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
