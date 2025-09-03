<?php
// public/dashboard.php
require_once __DIR__ . '/../config/config.php';
use Core\Auth;
if (!Auth::check()) { header('Location: /login.php'); exit; }
$u = Auth::user();
?>
<!doctype html>
<html lang="pt-br">
<head>
<link rel="manifest" href="/manifest.webmanifest">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>IdosoMS — Dashboard</title>
<link rel="stylesheet" href="/assets/css/styles.css">
<script defer src="/assets/js/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script defer src="/assets/js/dashboard.js"></script>
<script defer src="/assets/js/pwa.js"></script>
</head>
<body>
  <?php include BASE_PATH . '/views/partials/nav.php'; ?>
  <main class="container">
<?php if(($u['role']??'')==='admin_estadual'): ?>
<p class="muted">Registros limitados ao Estado (prefixo IBGE <strong><?php echo STATE_IBGE_PREFIX; ?></strong>).</p>
<?php endif; ?>
    <h1>Dashboard</h1>
    <div class="grid-3">
      <div class="card">
        <h2>Total de Pacientes</h2>
        <p class="kpi"><?php echo (int)($totPatients ?? 0); ?></p>
      </div>
      <div class="card">
        <h2>Total de Avaliações</h2>
        <p class="kpi"><?php echo (int)($totEval ?? 0); ?></p>
      </div>
      <div class="card">
        <h2>Usuário</h2>
        <p><?php echo htmlspecialchars($u['name']); ?> (<?php echo htmlspecialchars($u['role']); ?>)</p>
      </div>
    </div>
    <form id="filtersForm" class="card" style="margin:16px 0; display:grid; gap:12px; grid-template-columns: repeat(5, 1fr);">
  <div><label>Período (de)</label><input type="date" id="fromDate"></div>
  <div><label>Período (até)</label><input type="date" id="toDate"></div>
  <div><label>Tipo</label><select id="selType"><option value="">Todos</option><option>IVCF20</option><option>IVSF10</option></select></div>
  <div><label>Município (IBGE)</label><input type="text" id="munCode" placeholder="ex.: 500270"></div>
  <div style="align-self:end;"><button type="button" id="btnApply">Aplicar</button></div>
</form>
<section class="grid-3" style="margin-top:16px;">
    <div class="card"><canvas id="chartByMonth" height="180"></canvas></div>
    <div class="card"><canvas id="chartByClass" height="180"></canvas></div>
    <div class="card">
      <h2>Exportações</h2>
      <p><a class="btn-link" href="/export_csv.php">Baixar CSV (todas)</a></p>
<p><a class="btn-link" href="/export_excel.php">Baixar Excel (todas)</a></p>
<p><a class="btn-link" href="/export_excel.php?type=IVCF20">Baixar Excel IVCF-20</a></p>
<p><a class="btn-link" href="/export_excel.php?type=IVSF10">Baixar Excel IVSF-10</a></p>
      <p><a class="btn-link" href="/export_csv.php?type=IVCF20">Baixar CSV IVCF-20</a></p>
      <p><a class="btn-link" href="/export_csv.php?type=IVSF10">Baixar CSV IVSF-10</a></p>
    </div>
  </section>
</main>
</body>
</html>
