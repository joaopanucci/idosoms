<?php
// src/Controllers/DashboardController.php
namespace Controllers;
use Core\Auth;
use Core\DB;

class DashboardController {
    public static function show(): void {
        if (!Auth::check()) { header('Location: /login.php'); exit; }
        // Dados simples
        $pdo = DB::conn();
        $u = Auth::user();
        $totPatients = $pdo->query('SELECT COUNT(*) AS c FROM patients')->fetch()['c'] ?? 0;
        $totEval = $pdo->query('SELECT COUNT(*) AS c FROM evaluations')->fetch()['c'] ?? 0;
        require BASE_PATH . '/public/dashboard.php';
    }
}
