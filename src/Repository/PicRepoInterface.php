<?php

declare(strict_types=1);

namespace Salle\PixSalle\Repository;

interface PicRepoInterFace
{
    public function createPic($usrId, string $url): void;
    public function getAllPics();
    public function getUserPic(string $id);
}
