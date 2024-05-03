<?php

namespace Opanjr\LoginManagement\Config;

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
  public function testGetConnection()
  {

    $connecion = Database::getConnection();
    self::assertNotNull($connecion);
  }

  public function testGetConnectionSingleton()
  {

    $connection1 = Database::getConnection();
    $connection2 = Database::getConnection();
    self::assertSame($connection1, $connection2);
  }
}
