<?php

namespace Opanjr\LoginManagement\Config;

class Database
{

  private static ?\PDO $pdo = null;

  public static function getConnection(string $env = "test"): \PDO
  {
    if (self::$pdo == null) {
      // create new PDO
      require_once __DIR__ . "/../../config/database.php";
      $config = getDatabaseConfig();
      self::$pdo = new \PDO(
        $config["database"][$env]["url"],
        $config["database"][$env]["username"],
        $config["database"][$env]["password"]
      );
    }

    return self::$pdo;
  }

  // untuk memulai transaction
  public static function beginTransaction()
  {
    self::$pdo->beginTransaction();
  }

  // untuk melakukan transaction
  public static function commitTransaction()
  {
    self::$pdo->commit();
  }

  // untuk membatalkan transaction
  public static function rollbackTransaction()
  {
    self::$pdo->rollBack();
  }
}
