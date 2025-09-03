<?php
// public/evaluations_list.php
require_once __DIR__ . '/../config/config.php';
use Core\Auth;
use Models\Evaluation;

if (!Auth::check()) { header('Location: /login.php'); exit; }
$u = Auth::user();
$type = ($_GET['type'] ?? null);
$rows = Evaluation::listByUser($u['id'], $type);
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Avaliações</title>
<link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
  <?php include BASE_PATH . '/views/partials/nav.php'; ?>
  <main class="container">
    <h1>Avaliações <?php echo $type ? '(' . htmlspecialchars($type) . ')' : ''; ?></h1>
    <div class="filters">
      <a class="chip <?php echo $type===null?'active':''; ?>" href="/evaluations_list.php">Todas</a>
      <a class="chip <?php echo $type==='IVCF20'?'active':''; ?>" href="/evaluations_list.php?type=IVCF20">IVCF‑20</a>
      <a class="chip <?php echo $type==='IVSF10'?'active':''; ?>" href="/evaluations_list.php?type=IVSF10">IVSF‑10</a>
    </div>
    <div style="margin:8px 0 14px;">
      <a class="chip" href="/export_csv.php">CSV (todas)</a>
      <a class="chip" href="/export_csv.php?type=IVCF20">CSV IVCF-20</a>
      <a class="chip" href="/export_csv.php?type=IVSF10">CSV IVSF-10</a>
<a class="chip" href="/export_excel.php">Excel (todas)</a>
<a class="chip" href="/export_excel.php?type=IVCF20">Excel IVCF-20</a>
<a class="chip" href="/export_excel.php?type=IVSF10">Excel IVSF-10</a>
    </div>
    <table class="table">
      <thead><tr><th>ID</th><th>Paciente</th><th>Tipo</th><th>Score</th><th>Classificação</th><th>Criado em</th><th>Ações</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?php echo (int)$r['id']; ?></td>
            <td><?php echo htmlspecialchars($r['patient_name']); ?></td>
            <td><?php echo htmlspecialchars($r['type']); ?></td>
            <td><?php echo (int)$r['score']; ?></td>
            <td><?php echo htmlspecialchars($r['classification']); ?></td>
            <td><?php echo htmlspecialchars($r['created_at']); ?></td>
            <td>
              <a class="btn-link" href="/evaluations_view.php?id=<?php echo (int)$r['id']; ?>">Ver</a>
              | <a class="btn-link" href="/export_pdf.php?id=<?php echo (int)$r['id']; ?>">PDF</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </main>
</body>
</html>
