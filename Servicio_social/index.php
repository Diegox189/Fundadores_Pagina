<?php
require __DIR__ . '/db.php';
require __DIR__ . '/helpers.php';
$config = require __DIR__ . '/config.php';

session_start();

// si  no esta logueado redirige a login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol'])) {
    header('Location: login.php');
    exit;
}

$rol = $_SESSION['rol'];

$page = $_GET['page'] ?? 'dashboard';
$valid = ['dashboard','students','teachers','placements','logs','certificate'];
// define acceso por rol
$acceso = [
    'administrador' => $valid,
    'docente' => ['dashboard','students','placements','logs','certificate'],
    'estudiante' => ['dashboard','certificate']
];
if (!in_array($page, $acceso[$rol], true)) {
    $page = 'dashboard';
}
?><!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Servicio Social - Gestión</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="/Fundadores_Pagina/Servicio_social/assets/styles.css" rel="stylesheet">
  <link rel="shortcut icon" href="../Static/img/icono.png.png" type="image/x-icon">
</head>
<body>
  <header class="app-header">
    <div class="brand">Servicio Social</div>
    <nav class="topnav">
      <a href="http://localhost/Fundadores_Pagina/Templates/index.html" class="<?= $page==='dashboard'?'activo':'' ?>">Inicio - I.E. Fundadores</a>
      <a href="?page=dashboard" class="<?= $page==='dashboard'?'activo':'' ?>">Inicio</a>
      <?php if ($rol === 'administrador' || $rol === 'docente'): ?>
        <a href="?page=students" class="<?= $page==='students'?'activo':'' ?>">Estudiantes</a>
      <?php endif; ?>
      <?php if ($rol === 'administrador'): ?>
        <a href="?page=teachers" class="<?= $page==='teachers'?'activo':'' ?>">Docentes</a>
      <?php endif; ?>
      <?php if ($rol === 'administrador' || $rol === 'docente'): ?>
        <a href="?page=placements" class="<?= $page==='placements'?'activo':'' ?>">Acompañamientos</a>
        <a href="?page=logs" class="<?= $page==='logs'?'activo':'' ?>">Horas</a>
      <?php endif; ?>
      <a href="?page=certificate" class="<?= $page==='certificate'?'activo':'' ?>">Constancia</a>
      <a href="logout.php">Cerrar sesión</a>
    </nav>
  </header>

  <main class="container">
    <?php include __DIR__ . "/Pages/{$page}.php"; ?>
  </main>

  <footer class="app-footer">
    <small>&copy; <?= date('Y') ?> I.E. Fundadores — Módulo Servicio Social</small>
  </footer>
</body>
</html>