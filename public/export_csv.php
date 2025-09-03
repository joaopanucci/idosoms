<?php
// public/export_csv.php
require_once __DIR__ . '/../config/config.php';
use Core\Auth;
use Core\DB;

if (!Auth::check()) { http_response_code(401); exit('unauthorized'); }
$type = $_GET['type'] ?? null;
$pdo = DB::conn();
Audit::log($u['id'], 'export_csv', 'evaluation', null, $_SERVER['REMOTE_ADDR'] ?? null);
if ($type) {
    $stmt = $pdo->prepare("SELECT e.id, p.name as patient, e.type, e.score, e.classification, e.created_at
                           FROM evaluations e JOIN patients p ON p.id = e.patient_id
                           WHERE e.type = ? ORDER BY e.created_at DESC");
    $stmt->execute([$type]);
    $filename = "avaliacoes_{$type}.csv";
} else {
    $stmt = $pdo->query("SELECT e.id, p.name as patient, e.type, e.score, e.classification, e.created_at
                         FROM evaluations e JOIN patients p ON p.id = e.patient_id
                         ORDER BY e.created_at DESC");
    $filename = "avaliacoes_todas.csv";
}
$rows = $stmt->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="'.$filename.'"');
$out = fopen('php://output', 'w');
fputcsv($out, ['ID','Paciente','Tipo','Score','Classificação','Criado em'], ';');
foreach ($rows as $r) {
    fputcsv($out, [$r['id'], $r['patient'], $r['type'], $r['score'], $r['classification'], $r['created_at']], ';');
}
fclose($out);
exit;
