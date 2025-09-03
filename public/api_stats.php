<?php
require_once __DIR__ . '/../config/config.php';
use Core\Auth;
use Core\DB;

if (!Auth::check()) { http_response_code(401); echo json_encode(['error'=>'unauthorized']); exit; }

$from = $_GET['from'] ?? null;
$to   = $_GET['to'] ?? null;
$type = $_GET['type'] ?? null;
$mun  = $_GET['mun'] ?? null;

$where = []; $params = [];
if ($from) { $where[] = "e.created_at >= ?"; $params[] = $from . " 00:00:00"; }
if ($to)   { $where[] = "e.created_at <= ?"; $params[] = $to . " 23:59:59"; }
if ($type) { $where[] = "e.type = ?"; $params[] = $type; }
if ($mun)  { $where[] = "p.municipality_code = ?"; $params[] = $mun; }
$wsql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

$pdo = DB::conn();
$stmt = $pdo->prepare("SELECT DATE_FORMAT(e.created_at, '%Y-%m') as ym, e.type, COUNT(*) as c
                       FROM evaluations e JOIN patients p ON p.id = e.patient_id
                       $wsql
                       GROUP BY ym, e.type
                       ORDER BY ym ASC");
$stmt->execute($params);
$byMonth = $stmt->fetchAll();

$stmt2 = $pdo->prepare("SELECT e.type, e.classification, COUNT(*) as c
                        FROM evaluations e JOIN patients p ON p.id = e.patient_id
                        $wsql
                        GROUP BY e.type, e.classification
                        ORDER BY e.type ASC, c DESC");
$stmt2->execute($params);
$byClass = $stmt2->fetchAll();

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['byMonth'=>$byMonth, 'byClass'=>$byClass], JSON_UNESCAPED_UNICODE);
