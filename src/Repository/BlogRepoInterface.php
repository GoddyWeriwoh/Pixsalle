<?php

namespace Salle\PixSalle\Repository;

use Salle\PixSalle\Model\Blog;
interface BlogRepoInterface
{
    public function createBlog(Blog $blog):string;
    public function getAllBlogs();
    public function getRequestedBlog(string $id);
}