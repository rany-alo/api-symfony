<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Groupe;
use Doctrine\ORM\EntityManagerInterface;

class GroupeProcessor implements ProcessorInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {}
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if(false === $data instanceof Groupe) {
            return;
        }
        $data->setUpdatedAt(new \DateTimeImmutable());
        if($operation->getName() == "_api_/edit-groupe/{id}_patch") {
            $data->setCreatedAt($data->getCreatedAt());
        }
        else {
            $data->setCreatedAt(new \DateTimeImmutable());
        }


        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
