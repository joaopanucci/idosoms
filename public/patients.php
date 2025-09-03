<?php
// public/patients.php (placeholder de listagem/CRUD simples — pode evoluir)
require_once __DIR__ . '/../config/config.php';
use Core\Auth;
use Models\Patient;

if (!Auth::check()) { header('Location: /login.php'); exit; }
$u = Auth::user();
$rows = Patient::allByUser($u['id']);
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>IdosoMS — Pacientes</title>
<link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
  <?php include BASE_PATH . '/views/partials/nav.php'; ?>
  <main class="container">
    <h1>Pacientes (meus)</h1>
    <table class="table">
      <thead><tr><th>ID</th><th>Nome</th><th>Municipio</th><th>Criado em</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?php echo (int)$r['id']; ?></td>
            <td><?php echo htmlspecialchars($r['name']); ?></td>
            <td><?php echo htmlspecialchars($r['municipality_code'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($r['created_at']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </main>
</body>
</html>
