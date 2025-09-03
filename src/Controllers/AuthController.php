<?php
// src/Controllers/AuthController.php
namespace Controllers;
use Core\Auth;
use Core\CSRF;

class AuthController {
    public static function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cpf = $_POST['cpf'] ?? '';
            $password = $_POST['password'] ?? '';
            $token = $_POST['csrf_token'] ?? '';
            if (!CSRF::check($token)) {
                header('Location: /login.php?e=csrf');
                exit;
            }
            if (Auth::login($cpf, $password)) {
                header('Location: /dashboard.php');
                exit;
            }
            header('Location: /login.php?e=credenciais');
            exit;
        }
        require BASE_PATH . '/public/login.php';
    }

    public static function logout(): void {
        Auth::logout();
        header('Location: /login.php?logout=1');
        exit;
    }
}
