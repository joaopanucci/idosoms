<?php
// src/Models/Evaluation.php
namespace Models;

use Core\DB;
use PDO;

class Evaluation
{
    public static function create(array $data): int
    {
        $sql = 'INSERT INTO evaluations (patient_id, type, score, classification, answers, created_by) 
                VALUES (?, ?, ?, ?, ?, ?)';
        $stmt = DB::conn()->prepare($sql);
        $stmt->execute([
            $data['patient_id'],
            $data['type'],
            $data['score'],
            $data['classification'],
            json_encode($data['answers'], JSON_UNESCAPED_UNICODE),
            $data['created_by'],
        ]);
        return (int)DB::conn()->lastInsertId();
    }

    public static function listByScope(array $u, ?string $type = null): array
    {
        $pdo = \Core\DB::conn();
        $joins = ' FROM evaluations e JOIN patients p ON p.id = e.patient_id ';
        $where = [];
        $params = [];
        if ($type) {
            $where[] = 'e.type = ?';
            $params[] = $type;
        }
        list($scopesql, $scopeparams) = \Core\Scope::whereForUser($u);
        if ($scopesql) {
            $where[] = $scopesql;
            $params = array_merge($params, $scopeparams);
        }
        $wsql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        $sql = 'SELECT e.*, p.name AS patient_name ' . $joins . $wsql . ' ORDER BY e.id DESC LIMIT 500';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function findByIdForUser(int $id, int $userId): ?array
    {
        // Deprecated in v4: route now uses findByIdWithScope
        return self::findByIdWithScope($id, $_SESSION['user'] ?? ['id' => $userId]);
    }

    public static function findByIdWithScope(int $id, array $u): ?array
    {
        $pdo = \Core\DB::conn();
        $sql = 'SELECT e.*, p.name AS patient_name, p.cpf AS patient_cpf, p.birthdate, p.gender, p.municipality_code
                FROM evaluations e 
                JOIN patients p ON p.id = e.patient_id
                WHERE e.id = ?';
        $params = [$id];
        list($scopesql, $scopeparams) = \Core\Scope::whereForUser($u);
        if ($scopesql) {
            $sql .= ' AND ' . $scopesql;
            $params = array_merge($params, $scopeparams);
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        if ($row) {
            $row['answers'] = json_decode($row['answers'], true) ?: [];
        }
        return $row ?: null;
    }
}
