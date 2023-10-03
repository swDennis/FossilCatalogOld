<?php

namespace App\Services\Installation;

use App\Entity\User;
use App\Exceptions\CreateUserException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserService implements CreateUserServiceInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    public function createUser(string $email, string $plainPassword): User
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $user->setEmail($email);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);

        $user->setPassword($hashedPassword);

        return $user;
    }

    public function saveUser(User $user): void
    {
        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw new CreateUserException((string) $user->getEmail(), $exception);
        }
    }
}