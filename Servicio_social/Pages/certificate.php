<?php
// pages/certificate.php
$required = (float)$config['required_hours'];

$students = $pdo->query("
  SELECT id, CONCAT(first_name,' ',last_name,' (',grade,')') AS label
  FROM students ORDER BY last_name, first_name
")->fetchAll();

$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;

$student_name = '';
if ($student_id) {
  foreach ($students as $s) {
    if ($s['id'] == $student_id) {
      $student_name = explode('(', $s['label'])[0];
      $student_name = trim($student_name);
      break;
    }
  }
}

$placements = [];
$total = 0.0;
if ($student_id) {
  $placements = $pdo->prepare("
    SELECT p.id, t.name AS teacher, p.start_date, p.end_date, p.status,
           COALESCE(SUM(hl.hours),0) AS hours_done
    FROM placements p
    JOIN teachers t ON t.id = p.teacher_id
    LEFT JOIN hour_logs hl ON hl.placement_id = p.id AND hl.approved=1
    WHERE p.student_id = ?
    GROUP BY p.id
    ORDER BY p.start_date
  ");
  $placements->execute([$student_id]);
  $placements = $placements->fetchAll();
  foreach ($placements as $pl) $total += (float)$pl['hours_done'];
}
?>
<section>
  <h1>Constancia de Servicio Social</h1>

  <form method="get" class="form-inline">
    <input type="hidden" name="page" value="certificate">
    <label>Estudiante
      <select name="student_id" required>
        <option value="">Selecciona…</option>
        <?php foreach ($students as $s): ?>
          <option value="<?= (int)$s['id'] ?>" <?= $student_id===$s['id']?'selected':'' ?>><?= h($s['label']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <button class="btn-primary" type="submit">Consultar</button>
  </form>

  <?php if ($student_id): ?>
    <div class="card">
      <h2>Resumen</h2>
      <p><strong>Horas acumuladas:</strong> <?= (int)round($total) ?> / <?= (int)round($required) ?></p>
      <div class="table-responsive">
        <table>
          <thead><tr><th>Docente</th><th>Inicio</th><th>Fin</th><th>Estado</th><th>Horas</th></tr></thead>
          <tbody>
          <?php foreach ($placements as $pl): ?>
            <tr>
              <td><?= h($pl['teacher']) ?></td>
              <td><?= h($pl['start_date']) ?></td>
              <td><?= h($pl['end_date']) ?></td>
              <td><span class="badge badge-<?= h($pl['status']) ?>"><?= h($pl['status']) ?></span></td>
              <td><?= h((int)round($pl['hours_done'])) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php if ($total >= $required): ?>
        <div class="printable" id="constancia">

           <h3>CONSTANCIA DE SERVICIO SOCIAL ESTUDIANTIL OBLIGATORIO</h3>
          <p>
La Institución Educativa Fundadores, en cumplimiento de lo establecido en la Ley 115 de 1994 – Ley General de Educación (artículo 97), y en la Resolución 4210 de 1996 del Ministerio de Educación Nacional, que reglamenta la organización y desarrollo del Servicio Social Estudiantil Obligatorio en Colombia.
<br>
<br>
HACE CONSTAR
<br>
<br>
Que el(la) estudiante <strong><?= h($student_name) ?></strong>, identificado(a) en los registros académicos de esta institución, ha cumplido satisfactoriamente con un total de <strong><?= number_format($total,2) ?></strong> de Servicio Social Estudiantil Obligatorio, de conformidad con lo establecido en la normatividad vigente.
<br>
<br>
El estudiante desarrolló sus actividades en calidad de apoyo al docente: <strong><?= h($pl['teacher']) ?></strong>, participando en labores de acompañamiento pedagógico y colaborativo, de acuerdo con el formato institucional de seguimiento y los acompañamientos registrados por el área responsable.
<br>
La presente certificación se expide para constancia de cumplimiento de este requisito académico y formativo.
<br>
<br>
Se firma en la ciudad de Medellín (Antioquia), el dia de: <?= date('Y-m-d') ?>.
<br><br>
 ______________________________  
Coordinación Académica
<br>
 ______________________________
Rector(a)
<br><br>
          </p>
        </div>
        <br>
        <a class="btn" href="Pages/certificate_pdf.php?student_id=<?= (int)$student_id ?>" target="_blank">Descargar PDF</a>
      <?php else: ?>
        <p class="muted">Aún no cumple el mínimo de <?= number_format($required,2) ?> horas para generar la constancia.</p>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</section>
