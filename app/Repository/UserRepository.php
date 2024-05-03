<?php

namespace Opanjr\LoginManagement\Repository;

use Opanjr\LoginManagement\Domain\User;

class UserRepository
{

  private \PDO $connection;
  public function __construct(\PDO $connection)
  {
    $this->connection = $connection;
  }

  // untuk melakukan registrasi cukup masukkan data ke dlm database
  public function save(User $user): User
  {
    // melakukan query ke database
    $statement = $this->connection->prepare("INSERT INTO users(id, name, password) VALUES (?, ?, ?)");
    $statement->execute([
      $user->id, $user->name, $user->password
    ]);
    return $user;
  }


  // update User profile
  public function update(User $user): User
  {
    $statement = $this->connection->prepare("UPDATE users SET name = ?, password = ? WHERE id = ?");
    $statement->execute([
      $user->name, $user->password, $user->id
    ]);
    return $user;
  }

  // untuk melakukan pengecekan apakah data user sudah masuk/tidak
  public function findById(string $id): ?User
  {
    $statement = $this->connection->prepare("SELECT id, name, password FROM users WHERE id = ?");
    $statement->execute([$id]);

    try {
      if ($row = $statement->fetch()) {
        $user = new User();
        $user->id = $row['id'];
        $user->name = $row['name'];
        $user->password = $row['password'];
        return $user;
      } else {
        return null;
      }
    } finally {
      $statement->closeCursor();
    }
  }

  // function untuk melakukan delete semua datanya di unitest
  public function deleteAll(): void
  {
    $this->connection->exec("DELETE FROM users");
  }
}
