<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use DateTime;
use PDO;
use Salle\PixSalle\Model\User;
use Salle\PixSalle\Repository\UserRepository;

final class MySQLUserRepository implements UserRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDO $databaseConnection;

    public function __construct(PDO $database)
    {
        $this->databaseConnection = $database;
    }

    public function createUser(User $user): void
    {
        $query = <<<'QUERY'
        INSERT INTO users(email, password, createdAt, updatedAt, balance, membership, portfolio)
        VALUES(:email, :password, :createdAt, :updatedAt, :balance, "cool", NULL)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $email = $user->email();
        $password = $user->password();
        $balance = $user->balance();
        $createdAt = $user->createdAt()->format(self::DATE_FORMAT);
        $updatedAt = $user->updatedAt()->format(self::DATE_FORMAT);

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('createdAt', $createdAt, PDO::PARAM_STR);
        $statement->bindParam('updatedAt', $updatedAt, PDO::PARAM_STR);
        $statement->bindParam('balance', $balance, PDO::PARAM_STR);

        $statement->execute();

        $id = $this->getUserByEmail($email)->id;
        $this->updateUserById($id, null, "user" . $id, null);
    }

    public function getUserByEmail(string $email)
    {
        $query = <<<'QUERY'
        SELECT * FROM users WHERE email = :email
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);

        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetch(PDO::FETCH_OBJ);
            return $row;
        }
        return null;
    }

    public function getUserById(string $id){
        $query = <<<'QUERY'
        SELECT * FROM users WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetch(PDO::FETCH_OBJ);
            return $row;
        }
        return null;
    }

    public function updateUserById(string $id, $phone, $usr, $photo){
        $query = <<<'QUERY'
        UPDATE users SET phone = :phone, username = :username, updatedAt = :up, photo = :photo WHERE id = :id
        QUERY;
        $statement = $this->databaseConnection->prepare($query);
        $now = (new DateTime())->format(self::DATE_FORMAT);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->bindParam('phone', $phone, PDO::PARAM_STR);
        $statement->bindParam('username', $usr, PDO::PARAM_STR);
        $statement->bindParam('up', $now, PDO::PARAM_STR);
        $statement->bindParam('photo', $photo, PDO::PARAM_STR);

        $statement->execute();
    }

    public function updateUserPass(string $id, $pass){
        $query = <<<'QUERY'
        UPDATE users SET password = :password, updatedAt = :up WHERE id = :id
        QUERY;
        $statement = $this->databaseConnection->prepare($query);
        $now = (new DateTime())->format(self::DATE_FORMAT);
        $tmp = md5($pass);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->bindParam('up', $now, PDO::PARAM_STR);
        $statement->bindParam('password', $tmp, PDO::PARAM_STR);

        $statement->execute();
    }

    public function updateUserMembership(string $id, $mem){
        $query = <<<'QUERY'
        UPDATE users SET membership = :membership, updatedAt = :up WHERE id = :id
        QUERY;
        $statement = $this->databaseConnection->prepare($query);
        $now = (new DateTime())->format(self::DATE_FORMAT);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->bindParam('up', $now, PDO::PARAM_STR);
        $statement->bindParam('membership', $mem, PDO::PARAM_STR);

        $statement->execute();
    }
    
    public function updateUserBalance(string $id, string $balance)
    {
        $query = <<<'QUERY'
        UPDATE users SET balance = :balance WHERE id = :id
        QUERY;
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->bindParam('balance', $balance, PDO::PARAM_STR);
        $statement->execute();
    }
    
    public function updatePortfolio(string $id, string $val){
        $query = <<<'QUERY'
        UPDATE users SET portfolio = :val WHERE id = :id
        QUERY;
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->bindParam('val', $val, PDO::PARAM_STR);
        $statement->execute();
    }
}
