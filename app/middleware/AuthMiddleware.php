<?php

namespace App\middleware;

use App\controllers\AuthController;

class AuthMiddleware
{
    public static function checkAuth()
    {
        if (!AuthController::checkAuth()) {
            header('Location: /login');
            exit;
        }
    }

    public static function redirectIfAuthenticated()
    {
        if (AuthController::checkAuth()) {
            header('Location: /dashboard');
            exit;
        }
    }
}