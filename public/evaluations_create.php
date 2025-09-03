<?php
// public/evaluations_create.php
require_once __DIR__ . '/../config/config.php';
use Core\Auth;
use Core\CSRF;

if (!Auth::check()) { header('Location: /login.php'); exit; }
$type = ($_GET['type'] ?? 'IVCF20') === 'IVSF10' ? 'IVSF10' : 'IVCF20';
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Nova Avaliação — <?php echo $type; ?></title>
<link rel="stylesheet" href="/assets/css/styles.css">
<script defer src="/assets/js/main.js"></script>
<?php if ($type === 'IVCF20'): ?>
  <script defer src="/assets/js/ivcf20.js"></script>
<?php else: ?>
  <script defer src="/assets/js/ivsf10.js"></script>
<?php endif; ?>
<script defer src="/assets/js/autocomplete.js"></script>
</head>
<body>
  <?php include BASE_PATH . '/views/partials/nav.php'; ?>
  <main class="container">
    <h1>Nova Avaliação — <?php echo $type; ?></h1>

    <form method="post" action="/evaluations_store.php" id="evalForm">
      <input type="hidden" name="csrf_token" value="<?php echo CSRF::token(); ?>">
      <input type="hidden" name="type" value="<?php echo $type; ?>">
      <input type="hidden" name="answers" id="answersInput">
      <input type="hidden" name="score" id="scoreInput">
      <input type="hidden" name="classification" id="classInput">

      <fieldset class="card">
        <legend>Dados do Paciente</legend>
        <div class="grid-3">
          <div>
            <label>Nome*</label>
            <input type="text" name="patient_name" required>
          </div>
          <div>
            <label>CPF</label>
            <input type="text" name="patient_cpf" maxlength="14" placeholder="000.000.000-00">
          </div>
          <div>
            <label>Data de Nascimento</label>
            <input type="date" name="patient_birthdate">
          </div>
          <div>
            <label>Gênero</label>
            <select name="patient_gender">
              <option value="">—</option>
              <option value="M">Masculino</option>
              <option value="F">Feminino</option>
              <option value="O">Outro</option>
            </select>
          </div>
          <div style="position:relative;">
            <label>Município</label>
            <input type="text" id="munName" placeholder="Digite o nome do município...">
            <div id="munList" class="ac-list"></div>
            <div class="hint">Selecione para preencher o código IBGE automaticamente.</div>
          </div>
          <div>
            <label>Código IBGE do Município</label>
            <input type="text" name="patient_municipality" placeholder="ex.: 500270" pattern="\d{6,7}">
          </div>
          <div>
            <label>CNES da Unidade</label>
            <input type="text" name="patient_unit_cnes" placeholder="ex.: 1234567" pattern="\d{7}" title="CNES deve ter exatamente 7 dígitos numéricos.">
            <div class="hint">Obrigatório 7 dígitos (ex.: 1234567)</div>
          </div>
          <div style="position:relative;">
            <label>Nome da Unidade</label>
            <input type="text" id="unitName" name="patient_unit_name" placeholder="UBS/ESF...">
            <div id="cnesList" class="ac-list"></div>
            <div class="hint">Pesquise pelo nome; o CNES será preenchido automaticamente.</div>
          </div>
        </div>
      </fieldset>

      <fieldset class="card">
        <legend>Questionário — <?php echo $type; ?></legend>
        <div id="questions"></div>
        <div class="result">
          <strong>Pontuação:</strong> <span id="score">0</span> — 
          <strong>Classificação:</strong> <span id="class">—</span>
        </div>
      </fieldset>

      <div class="actions">
        <button type="submit">Salvar Avaliação</button>
        <a href="/evaluations_list.php?type=<?php echo $type; ?>" class="btn-link">Cancelar</a>
      </div>
    </form>
  </main>
</body>
</html>
