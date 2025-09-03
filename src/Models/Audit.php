<?php
// src/Models/Audit.php
namespace Models;
use Core\DB;

class Audit {
    public static function log(int $userId, string $action, string $entity, ?int $entityId = null, ?string $ip = null): void {
        $stmt = DB::conn()->prepare('INSERT INTO audit_logs (user_id, action, entity, entity_id, ip) VALUES (?,?,?,?,?)');
        $stmt->execute([$userId, $action, $entity, $entityId, $ip]);
    }
}
