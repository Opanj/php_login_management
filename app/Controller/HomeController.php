<?php

namespace Opanjr\LoginManagement\Controller;

use Opanjr\LoginManagement\App\View;
use Opanjr\LoginManagement\Config\Database;
use Opanjr\LoginManagement\Repository\SessionRepository;
use Opanjr\LoginManagement\Repository\UserRepository;
use Opanjr\LoginManagement\Service\SessionService;

class HomeController
{
  // setelah login perlu melakukan pengecekan
  private SessionService $sessionService;

  public function __construct()
  {
    $connection = Database::getConnection();
    $sessionRepository = new SessionRepository($connection);
    $userRepository = new UserRepository($connection);
    $this->sessionService = new SessionService($sessionRepository, $userRepository);
  }

  public function index()
  {
    $user = $this->sessionService->current();
    if ($user == null) {
      // belum login
      View::render("Home/index", [
        "title" => "PHP Login Management"
      ]);
    } else {
      // sudah login
      View::render("Home/dashboard", [
        "title" => "Dashboard",
        "user" => [
          "name" => $user->name
        ]
      ]);
    }
  }
}
