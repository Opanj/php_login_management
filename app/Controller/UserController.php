<?php

namespace Opanjr\LoginManagement\Controller;

use Opanjr\LoginManagement\App\View;
use Opanjr\LoginManagement\Config\Database;
use Opanjr\LoginManagement\Exception\ValidationException;
use Opanjr\LoginManagement\Model\UserLoginRequest;
use Opanjr\LoginManagement\Model\UserPasswordUpdateRequest;
use Opanjr\LoginManagement\Model\UserProfileUpdateRequest;
use Opanjr\LoginManagement\Model\UserRegisterRequest;
use Opanjr\LoginManagement\Repository\SessionRepository;
use Opanjr\LoginManagement\Repository\UserRepository;
use Opanjr\LoginManagement\Service\SessionService;
use Opanjr\LoginManagement\Service\UserService;

class UserController
{
  // untuk postregister kita butuh userservice
  private UserService $userService;
  private SessionService $sessionService;

  public function __construct()
  {
    // User service 
    $connection = Database::getConnection();
    $userRepository = new UserRepository($connection);
    $this->userService = new UserService($userRepository);

    // session service
    $sessionRepository = new SessionRepository($connection);
    $this->sessionService = new SessionService($sessionRepository, $userRepository);
  }

  // User Controller Register
  public function register()
  {
    View::render("User/register", [
      "title" => "Register new user"
    ]);
  }

  // aksi untuk registernya
  public function postRegister()
  {
    $request = new UserRegisterRequest();
    $request->id = $_POST['id'];
    $request->name = $_POST['name'];
    $request->password = $_POST['password'];

    // try catch untuk menghendel error yang terjadi ketika user melakukan registrasi 
    try {
      $this->userService->register($request);
      // setelah register success maka kita akan redirect to login
      View::redirect("/users/login");
    } catch (ValidationException $exception) {
      View::render("User/register", [
        "title" => "Register new user",
        "error" => $exception->getMessage()
      ]);
    }
  }


  // User Controller Login
  public function login()
  {
    View::render("User/login", [
      "title" => "Login user"
    ]);
  }

  public function postLogin()
  {
    $request = new UserLoginRequest();
    $request->id = $_POST['id'];
    $request->password = $_POST['password'];

    try {
      // sebelum redirect kita set cookie
      $response = $this->userService->login($request);
      $this->sessionService->create($response->user->id);
      View::redirect("/");
    } catch (ValidationException $exception) {
      View::render("User/login", [
        "title" => "Login user",
        "error" => $exception->getMessage()
      ]);
    }
  }


  // User Controller Logout
  public function logout()
  {
    $this->sessionService->destroy();
    View::redirect("/");
  }


  // User Update Profile
  public function updateProfile()
  {
    $user = $this->sessionService->current();
    View::render("User/profile", [
      "title" => "Update user profile",
      "user" => [
        "id" => $user->id,
        "name" => $user->name
      ]
    ]);
  }

  public function postUpdateProfile()
  {
    $user = $this->sessionService->current();

    $request = new UserProfileUpdateRequest();
    $request->id = $user->id;
    $request->name = $_POST['name'];

    try {
      $this->userService->updateProfile($request);
      View::redirect("/");
    } catch (ValidationException $exception) {
      View::render("User/profile", [
        "title" => "Update user profile",
        "error" => $exception->getMessage(),
        "user" => [
          "id" => $user->id,
          "name" => $_POST['name']
        ]
      ]);
    }
  }


  // user update password
  public function updatePassword()
  {
    $user = $this->sessionService->current();
    View::render("User/password", [
      "title" => "Update user password",
      "user" => [
        "id" => $user->id,
      ]
    ]);
  }

  public function postUpdatePassword()
  {
    $user = $this->sessionService->current();

    $request = new UserPasswordUpdateRequest();
    $request->id = $user->id;
    $request->oldPassword = $_POST['oldPassword'];
    $request->newPassword = $_POST['newPassword'];

    try {
      $this->userService->updatePassword($request);
      View::redirect("/");
    } catch (ValidationException $exception) {
      View::render("User/password", [
        "title" => "Update user password",
        "error" => $exception->getMessage(),
        "user" => [
          "id" => $user->id,
        ]
      ]);
    }
  }
}
