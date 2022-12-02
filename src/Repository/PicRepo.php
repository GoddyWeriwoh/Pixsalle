<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

use DateTime;
use PDO;
use Salle\PixSalle\Repository\PicRepoInterFace;

final class PicRepo implements PicRepoInterFace
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDO $databaseConnection;

    public function __construct(PDO $database)
    {
        $this->databaseConnection = $database;
    }

    public function createPic($usrId, string $url): void
    {
        $query = <<<'QUERY'
        INSERT INTO pictures(usrId, createdAt, url)
        VALUES(:usrId, :createdAt, :url)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $now = (new DateTime())->format(self::DATE_FORMAT);

        $statement->bindParam('usrId', $usrId, PDO::PARAM_STR);
        $statement->bindParam('url', $url, PDO::PARAM_STR);
        $statement->bindParam('createdAt', $now, PDO::PARAM_STR);

        $statement->execute();
    }

    public function getAllPics()
    {
        $query = <<<'QUERY'
        SELECT * FROM pictures
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetchAll(PDO::FETCH_OBJ);
            return $row;
        }
        return null;
    }

    public function getUserPic(string $id)
    {
        $query = <<<'QUERY'
        SELECT * FROM pictures WHERE usrId = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            return $row;
        }
        return null;
    }
}
