<?php
// src/Models/Notification.php
namespace Models;
use Core\DB;

class Notification {
    public static function create(int $userId, string $title, string $body): int {
        $stmt = DB::conn()->prepare('INSERT INTO notifications (user_id, title, body) VALUES (?,?,?)');
        $stmt->execute([$userId, $title, $body]);
        return (int)DB::conn()->lastInsertId();
    }
    public static function listFor(int $userId, bool $onlyUnread = false): array {
        if ($onlyUnread) {
            $stmt = DB::conn()->prepare('SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY id DESC LIMIT 200');
            $stmt->execute([$userId]);
        } else {
            $stmt = DB::conn()->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY id DESC LIMIT 200');
            $stmt->execute([$userId]);
        }
        return $stmt->fetchAll();
    }
    public static function markRead(int $id, int $userId): void {
        $stmt = DB::conn()->prepare('UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
    }
    public static function markAllRead(int $userId): void {
        $stmt = DB::conn()->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?');
        $stmt->execute([$userId]);
    }
}
