<?php
// public/index.php
require_once __DIR__ . '/../config/config.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';

// Rotas mínimas
switch ($path) {
    case '/':
        header('Location: /dashboard.php');
        exit;
    case '/login.php':
        \Controllers\AuthController::login();
        break;
    case '/logout':
        \Controllers\AuthController::logout();
        break;
    case '/dashboard.php':
        \Controllers\DashboardController::show();
        break;
    case '/evaluations_create.php':
        \Controllers\EvaluationController::create();
        break;
    case '/evaluations_store.php':
        \Controllers\EvaluationController::store();
        break;
    case '/evaluations_list.php':
        \Controllers\EvaluationController::index();
        break;
    case '/patients.php':
        require_once BASE_PATH . '/public/patients.php';
        break;
    case '/notifications.php':
        require_once BASE_PATH . '/public/notifications.php';
        break;
    default:
        http_response_code(404);
        echo '404';
}
