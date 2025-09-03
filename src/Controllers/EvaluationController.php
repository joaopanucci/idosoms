<?php
// src/Controllers/EvaluationController.php
namespace Controllers;
use Core\Auth;
use Core\CSRF;
use Core\DB;
use Models\Patient;
use Models\Evaluation;
use Models\Audit;
use Models\Notification;

class EvaluationController {
    private static function validateCnes(?string $cnes): bool { if ($cnes===null || $cnes==='') return true; return (bool)preg_match('/^\d{7}$/', $cnes); }
    private static function validateIbge(?string $ibge): bool { if ($ibge===null || $ibge==='') return true; return (bool)preg_match('/^\d{6,7}$/', $ibge); }
    public static function create(): void {
        if (!Auth::check()) { header('Location: /login.php'); exit; }
        $type = ($_GET['type'] ?? 'IVCF20') === 'IVSF10' ? 'IVSF10' : 'IVCF20';
        require BASE_PATH . '/public/evaluations_create.php';
    }

    public static function store(): void {
        if (!Auth::check()) { header('Location: /login.php'); exit; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
        if (!CSRF::check($_POST['csrf_token'] ?? '')) { http_response_code(400); echo 'CSRF'; exit; }

        $u = Auth::user();
        // 1) Garantir/registrar paciente rápido
        $patientName = trim($_POST['patient_name'] ?? '');
        if ($patientName === '') { http_response_code(422); echo 'Nome do paciente é obrigatório.'; exit; }
                // Validações backend
        $ibge = $_POST['patient_municipality'] ?? null;
        $cnes = $_POST['patient_unit_cnes'] ?? null;
        if (!self::validateIbge($ibge)) { http_response_code(422); echo 'Código IBGE inválido (use 6 ou 7 dígitos).'; exit; }
        if (!self::validateCnes($cnes)) { http_response_code(422); echo 'CNES inválido (deve ter 7 dígitos).'; exit; }

        $patientId = Patient::create([
            'cpf' => preg_replace('/\D+/', '', $_POST['patient_cpf'] ?? ''),
            'name' => $patientName,
            'birthdate' => $_POST['patient_birthdate'] ?? null,
            'gender' => $_POST['patient_gender'] ?? null,
            'municipality_code' => $_POST['patient_municipality'] ?? null,
            'unit_cnes' => $_POST['patient_unit_cnes'] ?? null,
            'unit_name' => $_POST['patient_unit_name'] ?? null,
            'created_by' => $u['id'],
        ]);

        // 2) Avaliação
        $type = $_POST['type'] === 'IVSF10' ? 'IVSF10' : 'IVCF20';
        $answers = json_decode($_POST['answers'] ?? '[]', true);
        $score = (int)($_POST['score'] ?? 0);
        $classification = substr($_POST['classification'] ?? 'Indefinido', 0, 50);

        // 3) Servidor pode (opcionalmente) recalcular classificação conforme constants.php
        $classification = self::classifyServer($type, $score);

        Audit::log($u['id'], 'create', 'evaluation', null, $_SERVER['REMOTE_ADDR'] ?? null);
        $id = Evaluation::create([
            'patient_id' => $patientId,
            'type' => $type,
            'score' => $score,
            'classification' => $classification,
            'answers' => $answers,
            'created_by' => $u['id'],
        ]);

        Notification::create($u['id'], 'Avaliação salva', 'Uma nova avaliação do tipo ' . $type . ' foi registrada.');
        header('Location: /evaluations_list.php?ok=1&type=' . urlencode($type));
        exit;
    }

    public static function index(): void {
        if (!Auth::check()) { header('Location: /login.php'); exit; }
        $u = Auth::user();
        $type = ($_GET['type'] ?? null);
        $rows = \Models\Evaluation::listByScope($u, $type);
        require BASE_PATH . '/public/evaluations_list.php';
    }

    private static function classifyServer(string $type, int $score): string {
        $ranges = $type === 'IVSF10' ? IVSF10_CLASSIFICATION : IVCF20_CLASSIFICATION;
        foreach ($ranges as $r) {
            if ($score >= $r['min'] && $score <= $r['max']) { return $r['label']; }
        }
        return 'Indefinido';
    }
}
