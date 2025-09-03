<?php
// src/Models/User.php
namespace Models;
use Core\DB;
use PDO;

class User {
    public static function find(int $id): ?array {
        $stmt = DB::conn()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $u = $stmt->fetch();
        return $u ?: null;
    }
}
