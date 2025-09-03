<?php
// public/evaluations_view.php
require_once __DIR__ . '/../config/config.php';
use Core\Auth;
use Models\Evaluation;

if (!Auth::check()) { header('Location: /login.php'); exit; }
$u = Auth::user();
$id = (int)($_GET['id'] ?? 0);
$row = $id ? Evaluation::findByIdForUser($id, $u['id']) : null;
if (!$row) { http_response_code(404); echo 'Avaliação não encontrada.'; exit; }
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Avaliação #<?php echo (int)$row['id']; ?></title>
<link rel="stylesheet" href="/assets/css/styles.css">
<!-- jsPDF CDN -->
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
</head>
<body>
  <?php include BASE_PATH . '/views/partials/nav.php'; ?>
  <main class="container" id="eval-view">
    <h1>Detalhe da Avaliação</h1>
    <div class="card">
      <h2>Paciente</h2>
      <p><strong>Nome:</strong> <?php echo htmlspecialchars($row['patient_name']); ?></p>
      <p><strong>CPF:</strong> <?php echo htmlspecialchars($row['patient_cpf'] ?? ''); ?></p>
      <p><strong>Nascimento:</strong> <?php echo htmlspecialchars($row['birthdate'] ?? ''); ?></p>
      <p><strong>Município (IBGE):</strong> <?php echo htmlspecialchars($row['municipality_code'] ?? ''); ?></p>
    </div>
    <div class="card">
      <h2>Avaliação</h2>
      <p><strong>ID:</strong> <?php echo (int)$row['id']; ?></p>
      <p><strong>Tipo:</strong> <?php echo htmlspecialchars($row['type']); ?></p>
      <p><strong>Pontuação:</strong> <?php echo (int)$row['score']; ?></p>
      <p><strong>Classificação:</strong> <?php echo htmlspecialchars($row['classification']); ?></p>
      <p><strong>Criado em:</strong> <?php echo htmlspecialchars($row['created_at']); ?></p>
    </div>
    <div class="card">
      <h2>Respostas</h2>
      <ul>
        <?php foreach (($row['answers'] ?? []) as $k => $v): ?>
          <li><strong><?php echo htmlspecialchars($k); ?>:</strong> <?php echo htmlspecialchars((string)$v); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="actions">
      <button id="btnPdf">Exportar PDF</button>
      <a class="btn-link" href="/evaluations_list.php?type=<?php echo urlencode($row['type']); ?>">Voltar</a>
    </div>
  </main>

<script>
document.getElementById('btnPdf')?.addEventListener('click', () => {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ unit: 'pt', format: 'a4' });
  let y = 50;
  doc.setFontSize(16);
  doc.text('IdosoMS - Avaliação Detalhe', 40, y); y += 28;

  doc.setFontSize(12);
  doc.text('Paciente: <?php echo addslashes($row['patient_name']); ?>', 40, y); y += 18;
  doc.text('CPF: <?php echo addslashes($row['patient_cpf'] ?? ''); ?>', 40, y); y += 18;
  doc.text('Nascimento: <?php echo addslashes($row['birthdate'] ?? ''); ?>', 40, y); y += 18;
  doc.text('Município (IBGE): <?php echo addslashes($row['municipality_code'] ?? ''); ?>', 40, y); y += 24;

  doc.text('Avaliação', 40, y); y += 18;
  doc.text('ID: <?php echo (int)$row['id']; ?>', 40, y); y += 18;
  doc.text('Tipo: <?php echo addslashes($row['type']); ?>', 40, y); y += 18;
  doc.text('Pontuação: <?php echo (int)$row['score']; ?>', 40, y); y += 18;
  doc.text('Classificação: <?php echo addslashes($row['classification']); ?>', 40, y); y += 24;

  doc.text('Respostas:', 40, y); y += 16;
  const answers = <?php echo json_encode($row['answers'] ?? []); ?>;
  for (const [key, val] of Object.entries(answers)) {
    const line = key + ': ' + String(val);
    doc.text(line, 60, y);
    y += 16;
    if (y > 760) { doc.addPage(); y = 50; }
  }

  doc.save('avaliacao_<?php echo (int)$row['id']; ?>.pdf');
});
</script>
</body>
</html>
