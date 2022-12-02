<?php

namespace Salle\PixSalle\Model;

class Blog
{
    private string $title;
    private string $content;
    private int $userId;
    private string $author;


    public function __construct(
        string $title,
        string $content,
        int    $userId,
        string $author
    ) {
        $this->title = $title;
        $this->content = $content;
        $this->userId = $userId;
        $this->author = $author;

    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

}