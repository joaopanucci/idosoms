<?php
// src/Models/Patient.php
namespace Models;
use Core\DB;
use PDO;

class Patient {
    public static function create(array $data): int {
        $sql = 'INSERT INTO patients (cpf, name, birthdate, gender, municipality_code, unit_cnes, unit_name, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = DB::conn()->prepare($sql);
        $stmt->execute([
            $data['cpf'] ?? null,
            $data['name'],
            $data['birthdate'] ?? null,
            $data['gender'] ?? null,
            $data['municipality_code'] ?? null,
            $data['unit_cnes'] ?? null,
            $data['unit_name'] ?? null,
            $data['created_by'],
        ]);
        return (int)DB::conn()->lastInsertId();
    }

    public static function allByUser(int $userId): array {
        $stmt = DB::conn()->prepare('SELECT * FROM patients WHERE created_by = ? ORDER BY id DESC LIMIT 200');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
