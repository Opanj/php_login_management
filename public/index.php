<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Opanjr\LoginManagement\App\Router;
use Opanjr\LoginManagement\Config\Database;
use Opanjr\LoginManagement\Controller\HomeController;
use Opanjr\LoginManagement\Controller\UserController;
use Opanjr\LoginManagement\Middleware\MustLoginMiddleware;
use Opanjr\LoginManagement\Middleware\MustNotLoginMiddleware;

// untuk database kita tambahkan disini 
Database::getConnection('prod');

// Home Controller
Router::add('GET', '/', HomeController::class, 'index', []);

// User Controller
Router::add('GET', '/users/register', UserController::class, 'register', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/register', UserController::class, 'postRegister', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/login', UserController::class, 'login', [MustNotLoginMiddleware::class]);
Router::add('POST', '/users/login', UserController::class, 'postLogin', [MustNotLoginMiddleware::class]);
Router::add('GET', '/users/logout', UserController::class, 'logout', [MustLoginMiddleware::class]);
Router::add('GET', '/users/profile', UserController::class, 'updateProfile', [MustLoginMiddleware::class]);
Router::add('POST', '/users/profile', UserController::class, 'PostUpdateProfile', [MustLoginMiddleware::class]);
Router::add('GET', '/users/password', UserController::class, 'updatePassword', [MustLoginMiddleware::class]);
Router::add('POST', '/users/password', UserController::class, 'PostUpdatePassword', [MustLoginMiddleware::class]);


Router::run();
