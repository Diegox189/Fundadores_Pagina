<?php
// pages/dashboard.php
// Totales rápidos
$students = $pdo->query("SELECT COUNT(*) c FROM students")->fetch()['c'] ?? 0;
$teachers = $pdo->query("SELECT COUNT(*) c FROM teachers")->fetch()['c'] ?? 0;
$placements = $pdo->query("SELECT COUNT(*) c FROM placements")->fetch()['c'] ?? 0;
$logs = $pdo->query("SELECT COUNT(*) c FROM hour_logs")->fetch()['c'] ?? 0;

// Últimos registros
$latestLogs = $pdo->query("
  SELECT hl.*, s.first_name, s.last_name, t.name AS teacher
  FROM hour_logs hl
  JOIN placements p ON p.id = hl.placement_id
  JOIN students s ON s.id = p.student_id
  JOIN teachers t ON t.id = p.teacher_id
  ORDER BY hl.created_at DESC
  LIMIT 5
")->fetchAll();
?>
<section>
  <h1>Panel general</h1>
  <div class="stats-grid">
    <div class="card"><div class="stat"><?= (int)$students ?></div><div class="label">Estudiantes</div></div>
    <div class="card"><div class="stat"><?= (int)$teachers ?></div><div class="label">Docentes</div></div>
    <div class="card"><div class="stat"><?= (int)$placements ?></div><div class="label">Acompañamientos</div></div>
    <div class="card"><div class="stat"><?= (int)$logs ?></div><div class="label">Registros de horas</div></div>
  </div>

  <h2>Últimas horas registradas</h2>
  <div class="table-responsive">
    <table>
      <thead><tr>
        <th>Fecha</th><th>Estudiante</th><th>Docente</th><th>Inicio</th><th>Fin</th><th>Horas</th><th>Notas</th>
      </tr></thead>
      <tbody>
      <?php foreach ($latestLogs as $row): ?>
        <tr>
          <td><?= h($row['log_date']) ?></td>
          <td><?= h($row['first_name'].' '.$row['last_name']) ?></td>
          <td><?= h($row['teacher']) ?></td>
          <td><?= h($row['start_time']) ?></td>
          <td><?= h($row['end_time']) ?></td>
          <td><?=h((int)round($row['hours'])) ?></td>
          <td><?= h($row['notes']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
