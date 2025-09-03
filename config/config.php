<?php
// config/config.php
declare(strict_types=1);
session_start();

date_default_timezone_set('America/Campo_Grande');

// ===== Ajuste as credenciais do seu MySQL =====
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'idosoms');
define('DB_USER', 'root');
define('DB_PASS', '');

// Caminhos base (ajuste se necessário)
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__ . '/../public');
define('ASSETS_URL', '/assets'); // se estiver em subpasta, ajuste p.ex. /idosoms/assets

require_once BASE_PATH . '/config/constants.php';

// Autoload Composer (mPDF, PhpSpreadsheet)
$autoload = BASE_PATH . '/vendor/autoload.php';
if (file_exists($autoload)) { require_once $autoload; }

spl_autoload_register(function ($class) {
    $baseDir = BASE_PATH . '/src/';
    $file = $baseDir . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Protege contra sessão fixa
if (!isset($_SESSION['regenerated'])) {
    session_regenerate_id(true);
    $_SESSION['regenerated'] = true;
}
