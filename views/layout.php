<?php
// views/layout.php (não usado como template engine ainda; base para evoluir)
?><!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $title ?? 'IdosoMS'; ?></title>
<link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
  <?php include __DIR__ . '/partials/nav.php'; ?>
  <main class="container">
    <?php echo $content ?? ''; ?>
  </main>
</body>
</html>
