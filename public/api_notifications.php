<?php
// public/api_notifications.php
require_once __DIR__ . '/../config/config.php';
use Core\Auth;
use Models\Notification;

if (!Auth::check()) { header('Location: /login.php'); exit; }
$u = Auth::user();
$action = $_GET['action'] ?? '';
if ($action === 'mark_all') {
    Notification::markAllRead($u['id']);
} elseif ($action === 'mark') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id) Notification::markRead($id, $u['id']);
}
header('Location: /notifications.php');
exit;
