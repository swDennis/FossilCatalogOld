<?php

namespace App\Services\Installation;

use App\Entity\User;

interface CreateUserServiceInterface
{
    public function createUser(string $email, string $plainPassword): User;

    public function saveUser(User $user): void;
}