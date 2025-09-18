<?php
// index.php
require __DIR__ . '/db.php';
require __DIR__ . '/helpers.php';
$config = require __DIR__ . '/config.php';

$page = $_GET['page'] ?? 'dashboard';
$valid = ['dashboard','students','teachers','placements','logs','certificate'];
if (!in_array($page, $valid, true)) $page = 'dashboard';

?><!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Servicio Social - Gestión</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="/Fundadores_Pagina/Servicio_social/assets/styles.css?v=1" rel="stylesheet">
  <link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
</head>
<body>
  <header class="app-header">
    <div class="brand">Servicio Social</div>
    <nav class="topnav">
      <a href="http://localhost/Fundadores_Pagina/Templates/index.html" class="<?= $page==='dashboard'?'activo':'' ?>" class="<?= $page==='dashboard'?'active':'' ?>">Inicio - I.E. Fundadores</a>
      <a href="?page=dashboard" class="<?= $page==='dashboard'?'activo':'' ?>">Inicio</a>
      <a href="?page=students" class="<?= $page==='students'?'activo':'' ?>">Estudiantes</a>
      <a href="?page=teachers" class="<?= $page==='teachers'?'activo':'' ?>">Docentes</a>
      <a href="?page=placements" class="<?= $page==='placements'?'activo':'' ?>">Acompañamientos</a>
      <a href="?page=logs" class="<?= $page==='logs'?'activo':'' ?>">Horas</a>
      <a href="?page=certificate" class="<?= $page==='certificate'?'activ':'' ?>">Constancia</a>
    </nav>
  </header>

  <main class="container">
    <?php include __DIR__ . "/pages/{$page}.php"; ?>
  </main>

  <footer class="app-footer">
    <small>&copy; <?= date('Y') ?> I.E. Fundadores — Módulo Servicio Social</small>
  </footer>
</body>
</html>
