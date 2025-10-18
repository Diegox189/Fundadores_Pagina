<?php
require __DIR__ . '/db.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = $_POST['clave'] ?? '';

    // Nuevo: buscar usuario en varias tablas (roles, docentes, estudiantes)
    function findUserAcrossTables(PDO $pdo, string $usuario) {
        $tables = [
            ['table' => 'roles', 'type' => null],       // tiene columna rol
            ['table' => 'teachers', 'type' => 'docente'],
            ['table' => 'students', 'type' => 'estudiante'],
        ];

        foreach ($tables as $t) {
            if ($t['table'] === 'roles') {
                $sql = "SELECT id, usuario, clave, rol FROM roles WHERE usuario = ? LIMIT 1";
            } else {
                // asumimos que docentes/estudiantes tienen columnas id, usuario, clave
                $sql = "SELECT id, usuario, clave, NULL AS rol FROM {$t['table']} WHERE usuario = ? LIMIT 1";
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$usuario]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                // si la tabla no tiene rol, asignar tipo fijo (docente/estudiante)
                if ($row['rol'] === null || $row['rol'] === '') {
                    $row['rol'] = $t['type'] ?? null;
                }
                // anotar la tabla origen por si se necesita
                $row['__table'] = $t['table'];
                return $row;
            }
        }
        return false;
    }

    $user = findUserAcrossTables($pdo, $usuario);

    // Verificar contraseña: admite tanto hash (password_verify) como texto plano
    $authenticated = false;
    if ($user) {
        $stored = $user['clave'] ?? '';
        if (function_exists('password_verify') && password_verify($clave, $stored)) {
            $authenticated = true;
        } elseif ($clave === $stored) {
            $authenticated = true;
        }
    }

    if ($authenticated) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['usuario'] = $user['usuario'];
        // si rol viene vacío, asegurar un valor por defecto
        $_SESSION['rol'] = $user['rol'] ?? 'usuario';
        // opcional: guardar la tabla origen
        $_SESSION['user_table'] = $user['__table'] ?? 'roles';

        header('Location: index.php');
        exit;
    } else {
        $error = "Usuario o clave incorrectos";
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Iniciar sesión - Servicio Social</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    :root{--accent:#a0151e;--bg:#f7f7fa;--card:#fff;--muted:#888}
    *{box-sizing:border-box}
    body{margin:0;height:100vh;display:flex;align-items:center;justify-content:center;font-family:Segoe UI,Roboto,Arial,sans-serif;background:var(--bg)}
    .card{width:100%;max-width:360px;background:var(--card);border-radius:12px;box-shadow:0 6px 24px rgba(0,0,0,.08);padding:28px 22px;display:flex;flex-direction:column;align-items:center}
    .logo{width:56px;height:56px;border-radius:50%;object-fit:contain;margin-bottom:12px;box-shadow:0 4px 14px rgba(160,21,30,.06)}
    h1{margin:4px 0 18px;color:var(--accent);font-size:1.25rem}
    form{width:100%;display:flex;flex-direction:column;gap:12px}
    input{width:100%;padding:10px 12px;border:1px solid #e6e6e6;border-radius:8px;background:#fbfcfd;font-size:1rem}
    input:focus{outline:none;border-color:var(--accent);background:#fff}
    button{width:100%;padding:10px;border:0;border-radius:8px;background:var(--accent);color:#fff;font-weight:600;cursor:pointer}
    .error{width:100%;background:#fff0f0;color:var(--accent);border:1px solid #f3c7c9;padding:8px;border-radius:8px;text-align:center;font-size:.95rem;margin-bottom:6px}
    .foot{margin-top:12px;color:var(--muted);font-size:.88rem}
  </style>
</head>
<body>
  <div class="card" role="main">
    <img src="../Static/img/icono.png.png" alt="logo" class="logo">
    <h1>Servicio Social</h1>

    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off" novalidate>
      <input name="usuario" type="text" placeholder="Usuario" required autofocus>
      <input name="clave" type="password" placeholder="Contraseña" required>
      <button type="submit">Entrar</button>
    </form>

    <div class="foot">&copy; <?= date('Y') ?> I.E. Fundadores</div>
  </div>
</body>
</html>
