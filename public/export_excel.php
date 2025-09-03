<?php
// public/export_excel.php
require_once __DIR__ . '/../config/config.php';
use Core\Auth;
use Core\DB;

if (!Auth::check()) { http_response_code(401); exit('unauthorized'); }
if (!class_exists('PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "PhpSpreadsheet não encontrado. Rode `composer install` na raiz do projeto.";
    exit;
}
$type = $_GET['type'] ?? null;
$pdo = DB::conn();
Audit::log($u['id'], 'export_excel', 'evaluation', null, $_SERVER['REMOTE_ADDR'] ?? null);
if ($type) {
    $stmt = $pdo->prepare("SELECT e.id, p.name as patient, e.type, e.score, e.classification, e.created_at
                           FROM evaluations e JOIN patients p ON p.id = e.patient_id
                           WHERE e.type = ? ORDER BY e.created_at DESC");
    $stmt->execute([$type]);
    $filename = "avaliacoes_{$type}.xlsx";
} else {
    $stmt = $pdo->query("SELECT e.id, p.name as patient, e.type, e.score, e.classification, e.created_at
                         FROM evaluations e JOIN patients p ON p.id = e.patient_id
                         ORDER BY e.created_at DESC");
    $filename = "avaliacoes_todas.xlsx";
}
$rows = $stmt->fetchAll();

$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->fromArray(['ID','Paciente','Tipo','Score','Classificação','Criado em'], NULL, 'A1');
$sheet->fromArray($rows, NULL, 'A2');
$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);
$sheet->getColumnDimension('E')->setAutoSize(true);
$sheet->getColumnDimension('F')->setAutoSize(true);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$filename.'"');
$writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save('php://output');
exit;
