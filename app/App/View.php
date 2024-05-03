<?php

namespace Opanjr\LoginManagement\App;

// untuk menghendle view 
class View
{
  public static function render(string $view, $model)
  {
    require __DIR__ . "/../View/header.php";
    require __DIR__ . "/../View/" . $view . ".php";
    require __DIR__ . "/../View/footer.php";
  }

  public static function redirect(string $url)
  {
    header("Location: $url");
    exit();
  }
}
