<?php
// src/Core/CSRF.php
namespace Core;

class CSRF {
    public static function token(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function check(?string $token): bool {
        return $token && hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}
