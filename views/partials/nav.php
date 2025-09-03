<?php
// views/partials/nav.php
use Core\Auth;
$u = Auth::user();
?>
<nav class="topbar">
  <div class="brand">
    <img src="/assets/img/logo.svg" alt="IdosoMS">
    <span>IdosoMS</span>
  </div>
  <ul>
    <li><a href="/dashboard.php">Início</a></li>
    <li><a href="/patients.php">Pacientes</a></li>
    <li class="dropdown">
      <a href="#">Avaliações</a>
      <div class="menu">
        <a href="/evaluations_create.php?type=IVCF20">Nova IVCF‑20</a>
        <a href="/evaluations_create.php?type=IVSF10">Nova IVSF‑10</a>
        <hr>
        <a href="/evaluations_list.php">Listar todas</a>
      </div>
    </li>
      <li><a href="/notifications.php">Notificações</a></li>
  </ul>
  <div class="user"><button id="btnInstallPWA" class="btn-link">Instalar</button>
    <span><?php echo htmlspecialchars($u['name'] ?? ''); ?></span>
    <a class="logout" href="/logout">Sair</a>
  </div>
</nav>
