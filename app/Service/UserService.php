<?php

namespace Opanjr\LoginManagement\Service;

use Opanjr\LoginManagement\Config\Database;
use Opanjr\LoginManagement\Exception\ValidationException;
// untuk register
use Opanjr\LoginManagement\Model\UserRegisterRequest;
use Opanjr\LoginManagement\Model\UserRegisterResponse;
use Opanjr\LoginManagement\Repository\UserRepository;
use Opanjr\LoginManagement\Domain\User;
// untuk login
use Opanjr\LoginManagement\Model\UserLoginRequest;
use Opanjr\LoginManagement\Model\UserLoginResponse;
// untuk password
use Opanjr\LoginManagement\Model\UserPasswordUpdateRequest;
use Opanjr\LoginManagement\Model\UserPasswordUpdateResponse;
// update user
use Opanjr\LoginManagement\Model\UserProfileUpdateRequest;
use Opanjr\LoginManagement\Model\UserProfileUpdateResponse;

class UserService
{
  private UserRepository $userRepository;

  public function __construct(UserRepository $userRepository)
  {
    $this->userRepository = $userRepository;
  }

  // User Register
  public function register(UserRegisterRequest $request): UserRegisterResponse
  {
    $this->validateUserRegistrationRequest($request);

    // untuk melakukan data transaction kita perlu try catch dulu
    try {
      Database::beginTransaction();
      // jika sudah valid maka kita akan melakukan pengecekan data user 
      $user = $this->userRepository->findById($request->id);
      if ($user != null) {
        throw new ValidationException("User Id already exists"); // user id sudah ada
      }

      // mengambil data baru dari user
      $user = new User();
      $user->id = $request->id;
      $user->name = $request->name;
      $user->password = password_hash($request->password, PASSWORD_BCRYPT); // agar pasword tidak bisa dihack

      // lakukan save data user
      $this->userRepository->save($user);

      // setelah itu lakukan response
      $response = new UserRegisterResponse();
      $response->user = $user;

      // sebelum return kita commit
      Database::commitTransaction();
      return $response;
    } catch (\Exception $exception) {
      // jika terjadi error maka kita akan rollback
      Database::rollbackTransaction();
      throw $exception;
    }
  }

  // melakukan validasi untuk request user
  private function validateUserRegistrationRequest(UserRegisterRequest $request)
  {
    if (
      $request->id == null || $request->name == null || $request->password == null ||
      trim($request->id) == "" || trim($request->name) == "" || trim($request->password) == ""
    ) {
      throw new ValidationException("Id, Name, Password can not blank"); // tidak boleh kosong
    }
  }


  // User Login
  public function login(UserLoginRequest $request): UserLoginResponse
  {
    $this->validateUserLoginRequest($request);

    $user = $this->userRepository->findById($request->id);
    if ($user == null) {
      throw new ValidationException("Id or password is wrong"); // id password salah
    }

    // cek untuk password
    if (password_verify($request->password, $user->password)) {
      $response = new UserLoginResponse();
      $response->user = $user;
      return $response;
    } else {
      throw new ValidationException("Id or password is wrong");
    }
  }

  private function validateUserLoginRequest(UserLoginRequest $request)
  {
    if (
      $request->id == null || $request->password == null ||
      trim($request->id) == "" || trim($request->password) == ""
    ) {
      throw new ValidationException("Id, Password can not blank"); // tidak boleh kosong
    }
  }


  // user update profile
  public function updateProfile(UserProfileUpdateRequest $request): UserProfileUpdateResponse
  {
    $this->validateUserUpdateProfileRequest($request);

    try {
      Database::beginTransaction();

      $user = $this->userRepository->findById($request->id);
      if ($user == null) {
        throw new ValidationException("User is not found");
      }

      $user->name = $request->name;
      $this->userRepository->update($user);

      Database::commitTransaction();

      $response = new UserProfileUpdateResponse();
      $response->user = $user;
      return $response;
    } catch (\Exception $exception) {
      Database::rollbackTransaction();
      throw $exception;
    }
  }

  private function validateUserUpdateProfileRequest(UserProfileUpdateRequest $request)
  {
    if (
      $request->id == null || $request->name == null ||
      trim($request->id) == "" || trim($request->name) == ""
    ) {
      throw new ValidationException("Id, Name can not blank"); // tidak boleh kosong
    }
  }


  // user update password
  public function updatePassword(UserPasswordUpdateRequest $request): UserPasswordUpdateResponse
  {
    $this->validateUserPasswordUpdateRequest($request);

    try {
      Database::beginTransaction();

      $user = $this->userRepository->findById($request->id);
      // tidak ada user
      if ($user == null) {
        throw new ValidationException("User is not found");
      }
      // user ada
      if (!password_verify($request->oldPassword, $user->password)) {
        throw new ValidationException("Old password is wrong"); //password lama salah
      }

      $user->password = password_hash($request->newPassword, PASSWORD_BCRYPT);
      $this->userRepository->update($user);

      Database::commitTransaction();

      $response = new UserPasswordUpdateResponse();
      $response->user = $user;
      return $response;
    } catch (\Exception $exception) {
      Database::rollbackTransaction();
      throw $exception;
    }
  }

  private function validateUserPasswordUpdateRequest(UserPasswordUpdateRequest $request)
  {
    if (
      $request->id == null || $request->oldPassword == null || $request->newPassword == null ||
      trim($request->id) == "" || trim($request->oldPassword) == "" || trim($request->newPassword) == ""
    ) {
      throw new ValidationException("Id, Old Password, New Password can not blank"); // tidak boleh kosong
    }
  }
}
