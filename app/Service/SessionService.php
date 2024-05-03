<?php

namespace Opanjr\LoginManagement\Service;

use Opanjr\LoginManagement\Domain\Session;
use Opanjr\LoginManagement\Domain\User;
use Opanjr\LoginManagement\Repository\SessionRepository;
use Opanjr\LoginManagement\Repository\UserRepository;

class SessionService
{
  // membuat cookie
  public static string $COOKIE_NAME = "X-PZN-SESSION";

  private SessionRepository $sessionRepository;
  private UserRepository $userRepository;

  public function __construct(SessionRepository $sessionRepository, UserRepository $userRepository)
  {
    $this->sessionRepository = $sessionRepository;
    $this->userRepository = $userRepository;
  }
  // membuat session
  public function create(string $userId): Session
  {
    $session = new Session();
    $session->id = uniqid();
    $session->userId = $userId;

    $this->sessionRepository->save($session);
    // untuk menyimpan data session gunakan cookie dan setelah login success set cookie
    setcookie(self::$COOKIE_NAME, $session->id, time() + (60 * 60 * 24 * 30), "/");

    return $session;
  }

  // menghapus session
  public function destroy()
  {
    $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? '';
    $this->sessionRepository->deleteById($sessionId);

    setcookie(self::$COOKIE_NAME, "", 1, "/");
  }

  // session user yang login saat ini
  public function current(): ?User
  {
    $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? '';

    $session = $this->sessionRepository->findById($sessionId);
    if ($session == null) {
      return null;
    }

    return $this->userRepository->findById($session->userId);
  }
}
