<?php

namespace Salle\PixSalle\Repository;

use DateTime;
use PDO;
use Salle\PixSalle\Model\Blog;

final class BlogRepository implements BlogRepoInterface
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDO $databaseConnection;

    public function __construct(PDO $database)
    {
        $this->databaseConnection = $database;
    }

    public function createBlog(Blog $blog): string
    {
        $query = <<<'QUERY'
        INSERT INTO blogs(userId, title, content, author, createdAt, updatedAt)
        VALUES(:userId, :title, :content, :author, :createdAt, :updatedAt)
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $now = (new DateTime())->format(self::DATE_FORMAT);
        $title = $blog ->getTitle();
        $content = $blog ->getContent();
        $userId = $blog->getUserId();
        $author = $blog->getAuthor();
       // $author = $blog->getId();

        //$statement->bindParam('author', $author, PDO::PARAM_STR);
        $statement->bindParam('userId', $userId, PDO::PARAM_STR);
        $statement->bindParam('title', $title, PDO::PARAM_STR);
        $statement->bindParam('content', $content, PDO::PARAM_STR);
        $statement->bindParam('createdAt', $now, PDO::PARAM_STR);
        $statement->bindParam('updatedAt', $now, PDO::PARAM_STR);
        $statement->bindParam('author', $author, PDO::PARAM_STR);



        $statement->execute();
        return $now;
    }

    public function getAllBlogs()
    {
        $query = <<<'QUERY'
        SELECT * FROM blogs
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

    public function getRequestedBlog(string $id)
    {
        $query = <<<'QUERY'
        SELECT * FROM blogs WHERE id = :id
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

    public function checkUserId($user_id)
    {
        $query = <<<'QUERY'
        SELECT * FROM users WHERE id = :id
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('id', $user_id, PDO::PARAM_STR);

        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
           return true;
        }
        return false;
    }

    public function getBlogInfo(string $createdDate)
    {
        $query = <<<'QUERY'
        SELECT * FROM blogs WHERE createdAt = :createdDate
        QUERY;

        $statement = $this->databaseConnection->prepare($query);

        $statement->bindParam('createdDate', $createdDate, PDO::PARAM_STR);

        $statement->execute();

        $count = $statement->rowCount();
        if ($count > 0) {
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            return $row;
        }
        return null;
    }

    public function updateBlog($id, $newInfo)
    {
        //$blog = $this->getRequestedBlog($id);
        $query = <<<'QUERY'
        UPDATE blogs
        SET
        userId = :userId,
            title = :title,
            content = :content,
            author = :author,
            updatedAt = :updatedAt
        WHERE 
            id = :id;
        QUERY;

        $statement = $this->databaseConnection->prepare($query);
        $now = (new DateTime())->format(self::DATE_FORMAT);
        $statement->bindParam('updatedAt', $now, PDO::PARAM_STR);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->bindParam('userId', $newInfo['userId'], PDO::PARAM_STR);
        $statement->bindParam('title', $newInfo['title'], PDO::PARAM_STR);
        $statement->bindParam('content', $newInfo['content'], PDO::PARAM_STR);
        $statement->bindParam('author', $newInfo['author'], PDO::PARAM_STR);

        $statement->execute();
    }

    public function deleteBlog($id)
    {
        //$blog = $this->getRequestedBlog($id);
        $query = <<<'QUERY'
        DELETE FROM blogs
        WHERE 
            id = :id;
        QUERY;
        $statement = $this->databaseConnection->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->execute();
    }
}