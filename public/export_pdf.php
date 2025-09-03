<?php
// public/export_pdf.php
require_once __DIR__ . '/../config/config.php';
use Core\Auth;
use Models\Evaluation;

if (!Auth::check()) { http_response_code(401); exit('unauthorized'); }
$id = (int)($_GET['id'] ?? 0);
$u = Auth::user();
$row = $id ? Evaluation::findByIdForUser($id, $u['id']) : null;
Audit::log($u['id'], 'export_pdf', 'evaluation', $id, $_SERVER['REMOTE_ADDR'] ?? null);
if (!$row) { http_response_code(404); exit('not found'); }

if (!class_exists('\Mpdf\Mpdf')) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "mPDF não encontrado. Rode `composer install` na raiz do projeto.";
    exit;
}

$html = '<h1 style="font-family: sans-serif;">IdosoMS - Avaliação</h1>';
$html .= '<h3>Paciente</h3>';
$html .= '<p><strong>Nome:</strong> '.htmlspecialchars($row['patient_name']).'</p>';
$html .= '<p><strong>CPF:</strong> '.htmlspecialchars($row['patient_cpf'] ?? '').'</p>';
$html .= '<p><strong>Nascimento:</strong> '.htmlspecialchars($row['birthdate'] ?? '').'</p>';
$html .= '<p><strong>Município (IBGE):</strong> '.htmlspecialchars($row['municipality_code'] ?? '').'</p>';
$html .= '<h3>Avaliação</h3>';
$html .= '<p><strong>ID:</strong> '.(int)$row['id'].' | <strong>Tipo:</strong> '.htmlspecialchars($row['type']).'</p>';
$html .= '<p><strong>Pontuação:</strong> '.(int)$row['score'].' | <strong>Classificação:</strong> '.htmlspecialchars($row['classification']).'</p>';
$html .= '<p><strong>Criado em:</strong> '.htmlspecialchars($row['created_at']).'</p>';
$html .= '<h3>Respostas</h3><ul>';
foreach (($row['answers'] ?? []) as $k => $v) {
  $html .= '<li><strong>'.htmlspecialchars($k).':</strong> '.htmlspecialchars((string)$v).'</li>';
}
$html .= '</ul>';

$mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir()]);
$mpdf->WriteHTML($html);
$mpdf->Output('avaliacao_'.$row['id'].'.pdf', 'D'); // force download
exit;
