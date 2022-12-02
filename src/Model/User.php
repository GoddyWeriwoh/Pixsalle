<?php

declare(strict_types=1);

namespace Salle\PixSalle\Model;

use DateTime;

class User
{

  private int $id;
  private string $email;
  private string $password;
  private Datetime $createdAt;
  private Datetime $updatedAt;
    private float $balance;

  public function __construct(
    string $email,
    string $password,
    Datetime $createdAt,
    Datetime $updatedAt
  ) {
    $this->email = $email;
    $this->password = $password;
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
      $this->balance = 30;
  }

  public function id()
  {
    return $this->id;
  }

  public function email()
  {
    return $this->email;
  }

  public function password()
  {
    return $this->password;
  }

  public function createdAt()
  {
    return $this->createdAt;
  }

  public function updatedAt()
  {
    return $this->updatedAt;
  }

    public function balance()
    {
        return $this->balance;
    }
}
