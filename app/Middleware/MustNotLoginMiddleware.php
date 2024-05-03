<?php

namespace Opanjr\LoginManagement\Middleware;

use Opanjr\LoginManagement\App\View;
use Opanjr\LoginManagement\Config\Database;
use Opanjr\LoginManagement\Repository\SessionRepository;
use Opanjr\LoginManagement\Repository\UserRepository;
use Opanjr\LoginManagement\Service\SessionService;

class MustNotLoginMiddleware implements Middleware
{
  // harusnya belum login (sudah login) 
  private SessionService $sessionService;

  public function __construct()
  {

    $sessionRepository = new SessionRepository(Database::getConnection());
    $userRepository = new UserRepository(Database::getConnection());
    $this->sessionService = new SessionService($sessionRepository, $userRepository);
  }

  function before(): void
  {
    $user = $this->sessionService->current();
    if ($user != null) {
      View::redirect("/");
    }
  }
}
