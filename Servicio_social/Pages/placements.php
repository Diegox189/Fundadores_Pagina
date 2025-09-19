<?php
// pages/placements.php

// Datos para selects
$students = $pdo->query("SELECT id, first_name, last_name, grade FROM students ORDER BY last_name, first_name")->fetchAll();
$teachers = $pdo->query("SELECT id, name FROM teachers ORDER BY name")->fetchAll();

// CREATE/UPDATE/DELETE/STATUS
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $action = $_POST['action'] ?? '';
  if ($action==='create') {
    $days = parse_days($_POST['days'] ?? []);
    $stmt = $pdo->prepare("INSERT INTO placements(student_id, teacher_id, start_date, end_date, days_of_week, status)
                           VALUES(?,?,?,?,JSON_ARRAY(".implode(',', array_fill(0, count($days), '?'))."),?)");
    $params = [
      (int)$_POST['student_id'],
      (int)$_POST['teacher_id'],
      $_POST['start_date'],
      $_POST['end_date'],
      ...$days,
      $_POST['status'] ?? 'pending'
    ];
    $stmt->execute($params);
    header("Location: ?page=placements"); exit;
  }

  if ($action==='update') {
    $id = (int)$_POST['id'];
    $days = parse_days($_POST['days'] ?? []);
    // Construimos JSON_ARRAY dinámico
    $jsonFragment = count($days) ? "JSON_ARRAY(".implode(',', array_fill(0, count($days), '?')).")" : "JSON_ARRAY()";
    $stmt = $pdo->prepare("UPDATE placements
                           SET student_id=?, teacher_id=?, start_date=?, end_date=?, days_of_week=$jsonFragment, status=?
                           WHERE id=?");
    $params = [
      (int)$_POST['student_id'],
      (int)$_POST['teacher_id'],
      $_POST['start_date'],
      $_POST['end_date'],
      ...$days,
      $_POST['status'] ?? 'pending',
      $id
    ];
    $stmt->execute($params);
    header("Location: ?page=placements"); exit;
  }
}

// DELETE
if (($_GET['delete'] ?? '')!=='') {
  $id = (int)$_GET['delete'];
  $pdo->prepare("DELETE FROM placements WHERE id=?")->execute([$id]);
  header("Location: ?page=placements"); exit;
}

// EDIT fetch
$edit = null;
if (($_GET['edit'] ?? '')!=='') {
  $id = (int)$_GET['edit'];
  $stmt = $pdo->prepare("SELECT * FROM placements WHERE id=?");
  $stmt->execute([$id]);
  $edit = $stmt->fetch();
  $editDays = $edit ? (json_decode($edit['days_of_week'], true) ?: []) : [];
}

// LIST (incluye horas sumadas)
$rows = $pdo->query("
  SELECT p.*,
         CONCAT(s.first_name,' ',s.last_name,' (',s.grade,')') AS student_name,
         t.name AS teacher_name,
         COALESCE(SUM(hl.hours),0) AS hours_done
  FROM placements p
  JOIN students s ON s.id = p.student_id
  JOIN teachers t ON t.id = p.teacher_id
  LEFT JOIN hour_logs hl ON hl.placement_id = p.id
  GROUP BY p.id
  ORDER BY p.created_at DESC
")->fetchAll();

$daysAll = ['Lun','Mar','Mie','Jue','Vie','Sab','Dom'];
?>
<section>
  <h1>Acompañamientos</h1>

  <div class="grid-2">
    <div class="card">
      <h2><?= $edit ? 'Editar acompañamiento' : 'Nuevo acompañamiento' ?></h2>
      <form method="post" class="form-grid">
        <input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>">
        <?php if ($edit): ?>
          <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
        <?php endif; ?>

        <label>Estudiante
          <select name="student_id" required>
            <option value="">Selecciona…</option>
            <?php foreach ($students as $s): ?>
              <option value="<?= (int)$s['id'] ?>" <?= $edit && $edit['student_id']==$s['id']?'selected':'' ?>>
                <?= h($s['last_name'].', '.$s['first_name'].' ('.$s['grade'].')') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </label>

        <label>Docente
          <select name="teacher_id" required>
            <option value="">Selecciona…</option>
            <?php foreach ($teachers as $t): ?>
              <option value="<?= (int)$t['id'] ?>" <?= $edit && $edit['teacher_id']==$t['id']?'selected':'' ?>>
                <?= h($t['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </label>

        <label>Fecha inicio
          <input type="date" name="start_date" required value="<?= h($edit['start_date'] ?? '') ?>">
        </label>

        <label>Fecha fin
          <input type="date" name="end_date" required value="<?= h($edit['end_date'] ?? '') ?>">
        </label>

        <fieldset class="days">
          <legend>Días entre semana</legend>
          <?php foreach ($daysAll as $d): ?>
            <label class="check">
              <input type="checkbox" name="days[]" value="<?= $d ?>"
                <?= isset($editDays) && in_array($d, $editDays ?? [], true) ? 'checked' : '' ?>>
              <span><?= $d ?></span>
            </label>
          <?php endforeach; ?>
        </fieldset>

        <label>Estado
          <select name="status">
            <?php foreach (['pendiente','activo','completed','cancelado'] as $st): ?>
              <option value="<?= $st ?>" <?= $edit && $edit['status']===$st ? 'selected':'' ?>>
                <?= ucfirst($st) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </label>

        <div class="actions">
          <button type="submit" class="btn-primary">Guardar</button>
          <?php if ($edit): ?><a class="btn" href="?page=placements">Cancelar</a><?php endif; ?>
        </div>
      </form>
    </div>

    <div class="card">
      <h2>Listado</h2>
      <div class="table-responsive">
        <table>
          <thead><tr>
            <th>Estudiante</th><th>Docente</th><th>Fechas</th><th>Días</th><th>Estado</th><th>Horas</th><th>Acciones</th>
          </tr></thead>
          <tbody>
          <?php foreach ($rows as $r):
            $days = json_decode($r['days_of_week'], true) ?: [];
          ?>
            <tr>
              <td><?= h($r['student_name']) ?></td>
              <td><?= h($r['teacher_name']) ?></td>
              <td><?= h($r['start_date'].' → '.$r['end_date']) ?></td>
              <td><?= h(implode(', ', $days)) ?></td>
              <td><span class="badge badge-<?= h($r['status']) ?>"><?= h($r['status']) ?></span></td>
              <td><?= h((int)round($r['hours_done'])) ?></td>
              <td class="nowrap">
                <a class="btn" href="?page=placements&edit=<?= (int)$r['id'] ?>">Editar</a>
                <a class="btn-danger" href="?page=placements&delete=<?= (int)$r['id'] ?>" onclick="return confirm('¿Eliminar acompañamiento?');">Eliminar</a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
