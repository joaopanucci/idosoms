<?php
// src/Core/Auth.php
namespace Core;
use Core\DB;
use PDO;

class Auth {
    public static function user(): ?array {
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool {
        return isset($_SESSION['user']);
    }

    public static function login(string $cpf, string $password): bool {
        $cpf = preg_replace('/\D+/', '', $cpf);
        $pdo = DB::conn();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE cpf = ? LIMIT 1');
        $stmt->execute([$cpf]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = [
                'id' => (int)$user['id'],
                'name' => $user['name'],
                'cpf'  => $user['cpf'],
                'role' => $user['role'],
                'municipality_code' => $user['municipality_code'],
            ];
            $pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?')->execute([$user['id']]);
            \Models\Audit::log((int)$user['id'], 'login', 'user', (int)$user['id'], $_SERVER['REMOTE_ADDR'] ?? null);
            return true;
        }
        return false;
    }

    public static function logout(): void {
        if (isset($_SESSION['user']['id'])) { \Models\Audit::log((int)$_SESSION['user']['id'], 'logout', 'user', (int)$_SESSION['user']['id'], $_SERVER['REMOTE_ADDR'] ?? null); }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public static function requireRole(array $roles): void {
        if (!self::check() || !in_array($_SESSION['user']['role'] ?? '', $roles, true)) {
            header('Location: /login.php?e=permissao');
            exit;
        }
    }
}
