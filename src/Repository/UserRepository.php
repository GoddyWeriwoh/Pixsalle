<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use Salle\PixSalle\Model\User;

interface UserRepository
{
    public function createUser(User $user): void;
    public function getUserByEmail(string $email);
    public function getUserById(string $id);
    public function updateUserById(string $id, $phone, $usr, $photo);
    public function updateUserPass(string $id, $pass);
    public function updateUserBalance(string $id, string $balance);
    public function updatePortfolio(string $id, string $val);
}
