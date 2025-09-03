<?php
// public/notifications.php
require_once __DIR__ . '/../config/config.php';
use Core\Auth;
use Models\Notification;

if (!Auth::check()) { header('Location: /login.php'); exit; }
$u = Auth::user();
$rows = Notification::listFor($u['id']);
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Notificações</title>
<link rel="manifest" href="/manifest.webmanifest">
<link rel="stylesheet" href="/assets/css/styles.css">
<script defer src="/assets/js/pwa.js"></script>
</head>
<body>
  <?php include BASE_PATH . '/views/partials/nav.php'; ?>
  <main class="container">
    <h1>Notificações</h1>
    <div class="actions">
      <a class="btn-link" href="/api_notifications.php?action=mark_all">Marcar todas como lidas</a>
    </div>
    <table class="table">
      <thead><tr><th>ID</th><th>Título</th><th>Mensagem</th><th>Lida</th><th>Quando</th><th>Ações</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $n): ?>
          <tr>
            <td><?php echo (int)$n['id']; ?></td>
            <td><?php echo htmlspecialchars($n['title']); ?></td>
            <td><?php echo htmlspecialchars($n['body']); ?></td>
            <td><?php echo $n['is_read'] ? 'Sim' : 'Não'; ?></td>
            <td><?php echo htmlspecialchars($n['created_at']); ?></td>
            <td>
              <?php if (!$n['is_read']): ?>
                <a class="btn-link" href="/api_notifications.php?action=mark&id=<?php echo (int)$n['id']; ?>">Marcar lida</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </main>
</body>
</html>
