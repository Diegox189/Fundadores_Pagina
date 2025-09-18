<?php
// pages/logs.php

// Datos para selects
$placements = $pdo->query("
  SELECT p.id,
         CONCAT(s.first_name,' ',s.last_name,' (',s.grade,') - ', t.name) AS label
  FROM placements p
  JOIN students s ON s.id = p.student_id
  JOIN teachers t ON t.id = p.teacher_id
  ORDER BY p.id DESC
")->fetchAll();

// CREATE/UPDATE
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $action = $_POST['action'] ?? '';
  if ($action==='create' || $action==='update') {
    $placement_id = (int)$_POST['placement_id'];
    $log_date = $_POST['log_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $notes = trim($_POST['notes'] ?? '');
    $approved = 1;

    $hours = hours_between($log_date.' '.$start_time, $log_date.' '.$end_time);

    if ($action==='create') {
      $stmt = $pdo->prepare("INSERT INTO hour_logs(placement_id, log_date, start_time, end_time, hours, notes, approved)
                             VALUES(?,?,?,?,?,?,?)");
      $stmt->execute([$placement_id, $log_date, $start_time, $end_time, $hours, $notes, $approved]);
    } else {
      $id = (int)$_POST['id'];
      $stmt = $pdo->prepare("UPDATE hour_logs SET placement_id=?, log_date=?, start_time=?, end_time=?, hours=?, notes=?, approved=? WHERE id=?");
      $stmt->execute([$placement_id, $log_date, $start_time, $end_time, $hours, $notes, $approved, $id]);
    }
    header("Location: ?page=logs"); exit;
  }
}

// DELETE
if (($_GET['delete'] ?? '')!=='') {
  $id = (int)$_GET['delete'];
  $pdo->prepare("DELETE FROM hour_logs WHERE id=?")->execute([$id]);
  header("Location: ?page=logs"); exit;
}

// EDIT fetch
$edit = null;
if (($_GET['edit'] ?? '')!=='') {
  $id = (int)$_GET['edit'];
  $stmt = $pdo->prepare("SELECT * FROM hour_logs WHERE id=?");
  $stmt->execute([$id]);
  $edit = $stmt->fetch();
}

// LIST (últimos 50)
$rows = $pdo->query("
  SELECT hl.*, CONCAT(s.first_name,' ',s.last_name,' (',s.grade,')') AS student_name, t.name AS teacher_name
  FROM hour_logs hl
  JOIN placements p ON p.id = hl.placement_id
  JOIN students s ON s.id = p.student_id
  JOIN teachers t ON t.id = p.teacher_id
  ORDER BY hl.log_date DESC, hl.start_time DESC
  LIMIT 50
")->fetchAll();
?>
<section>
  <h1>Registro de horas</h1>

  <div class="grid-2">
    <div class="card">
      <h2><?= $edit ? 'Editar registro' : 'Nuevo registro' ?></h2>
      <form method="post" class="form-grid">
        <input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>">
        <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>

        <label>Acompañamiento
          <select name="placement_id" required>
            <option value="">Selecciona…</option>
            <?php foreach ($placements as $p): ?>
              <option value="<?= (int)$p['id'] ?>" <?= $edit && $edit['placement_id']==$p['id']?'selected':'' ?>>
                <?= h($p['label']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </label>

        <label>Fecha
          <input type="date" name="log_date" required value="<?= h($edit['log_date'] ?? '') ?>">
        </label>
        <label>Hora inicio
          <input type="time" name="start_time" required value="<?= h($edit['start_time'] ?? '') ?>">
        </label>
        <label>Hora fin
          <input type="time" name="end_time" required value="<?= h($edit['end_time'] ?? '') ?>">
        </label>
        <label>Notas
          <input name="notes" placeholder="p.ej., apoyo en clase 6B" value="<?= h($edit['notes'] ?? '') ?>">
        </label>

        <div class="actions">
          <button type="submit" class="btn-primary">Guardar</button>
          <?php if ($edit): ?><a class="btn" href="?page=logs">Cancelar</a><?php endif; ?>
        </div>
      </form>
      <p class="muted">Las horas se calculan automáticamente a partir de la hora de inicio y fin (con 2 decimales).</p>
    </div>

    <div class="card">
      <h2>Últimos registros</h2>
      <div class="table-responsive">
        <table>
          <thead>
            <tr><th>Fecha</th><th>Estudiante</th><th>Docente</th><th>Inicio</th><th>Fin</th><th>Horas</th><th>Aprob.</th><th>Acciones</th></tr>
          </thead>
          <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= h($r['log_date']) ?></td>
              <td><?= h($r['student_name']) ?></td>
              <td><?= h($r['teacher_name']) ?></td>
              <td><?= h($r['start_time']) ?></td>
              <td><?= h($r['end_time']) ?></td>
              <td><?= h((int)round($r['hours'])) ?></td>
              <td><?= $r['approved'] ? 'Sí' : 'No' ?></td>
              <td class="nowrap">
                <a class="btn" href="?page=logs&edit=<?= (int)$r['id'] ?>">Editar</a>
                <a class="btn-danger" href="?page=logs&delete=<?= (int)$r['id'] ?>" onclick="return confirm('¿Eliminar registro?');">Eliminar</a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
