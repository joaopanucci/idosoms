<?php
// src/Core/DB.php
namespace Core;
use PDO;
use PDOException;

class DB {
    private static ?PDO $conn = null;

    public static function conn(): PDO {
        if (self::$conn === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            try {
                self::$conn = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                die('Erro ao conectar no banco: ' . $e->getMessage());
            }
        }
        return self::$conn;
    }
}
