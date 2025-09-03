<?php
// public/login.php
require_once __DIR__ . '/../config/config.php';
use Core\CSRF;
if (isset($_SESSION['user'])) { header('Location: /dashboard.php'); exit; }
$err = $_GET['e'] ?? null;
?>
<!doctype html>
<html lang="pt-br">
<head>
<link rel="manifest" href="/manifest.webmanifest">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>IdosoMS — Login</title>
<link rel="stylesheet" href="/assets/css/styles.css">
<script defer src="/assets/js/cpf.js"></script>
<script defer src="/assets/js/pwa.js"></script>
</head>
<body class="bg">
  <div class="login-card">
    <img src="/assets/img/logo.svg" alt="IdosoMS" class="logo">
    <h1>Entrar</h1>
    <?php if($err): ?>
      <div class="alert">Falha no login. Verifique CPF/senha. (<?php echo htmlspecialchars($err); ?>)</div>
    <?php endif; ?>
    <form method="post" action="/login.php" novalidate>
      <input type="hidden" name="csrf_token" value="<?php echo CSRF::token(); ?>">
      <label>CPF</label>
      <input type="text" name="cpf" id="cpf" maxlength="14" placeholder="000.000.000-00" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}|\d{11}" title="Digite um CPF válido" required>
      <label>Senha</label>
      <input type="password" name="password" placeholder="••••••••" required>
      <button type="submit">Acessar</button>
      <p class="muted">Super Admin: CPF 000.000.000-00 | Senha: admin123</p>
    </form>
  </div>
</body>
</html>
