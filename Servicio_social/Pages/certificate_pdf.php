<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

use Dompdf\Dompdf;

// Obtener datos del estudiante
$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
if (!$student_id) {
    die("Estudiante no especificado.");
}

// Consulta de datos
$students = $pdo->query("
  SELECT id, CONCAT(first_name,' ',last_name,' (',grade,')') AS label
  FROM students ORDER BY last_name, first_name
")->fetchAll();

$student_name = '';
foreach ($students as $s) {
    if ($s['id'] == $student_id) {
        $student_name = explode('(', $s['label'])[0];
        $student_name = trim($student_name);
        break;
    }
}

$placements = [];
$total = 0.0;
$required = (float)$config['required_hours'];
if ($student_id) {
    $stmt = $pdo->prepare("
        SELECT p.id, t.name AS teacher, p.start_date, p.end_date, p.status,
               COALESCE(SUM(hl.hours),0) AS hours_done
        FROM placements p
        JOIN teachers t ON t.id = p.teacher_id
        LEFT JOIN hour_logs hl ON hl.placement_id = p.id AND hl.approved=1
        WHERE p.student_id = ?
        GROUP BY p.id
        ORDER BY p.start_date
    ");
    $stmt->execute([$student_id]);
    $placements = $stmt->fetchAll();
    foreach ($placements as $pl) $total += (float)$pl['hours_done'];
}

if ($total < $required) {
    die("El estudiante aún no cumple el mínimo de horas para generar la constancia.");
}

// Ruta pública de la imagen
$bg = "http://localhost/Fundadores_Pagina/Static/img/plantilla_fundadores.jpg";

// --- HTML del certificado ---
ob_start();
?>
<style>
body { 
  font-family: Arial, sans-serif; 
  font-size: 13px; 
  margin: 20px; 
  -webkit-print-color-adjust: exact !important;
  print-color-adjust: exact !important;
}
h3 { 
  text-align: center; 
}
.tabla { 
  width: 100%; 
  border-collapse: collapse;
  margin-top: 20px; }
.tabla th, .tabla td { 
  border: 1px solid #888; 
  padding: 4px 8px; 
  text-align: center; 
}
</style>

<!-- Fondo detrás del contenido -->
<div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            z-index: -1; opacity: 0.7;">
  <img src="<?= $bg ?>" style="width: 100%; height: 100%; object-fit: cover;">
</div>

<!-- Contenido del certificado -->
<div style="padding: 100px 80px 20px 80px; background: transparent;">
</div>
<<h3 style="text-align: center;">CONSTANCIA DE SERVICIO SOCIAL ESTUDIANTIL OBLIGATORIO</h3>
    <p style="text-align: justify;">
        La Institución Educativa Fundadores, en cumplimiento de lo establecido...
    </p>
<p>
La Institución Educativa Fundadores, en cumplimiento de lo establecido en la Ley 115 de 1994 – Ley General de Educación (artículo 97), y en la Resolución 4210 de 1996 del Ministerio de Educación Nacional, que reglamenta la organización y desarrollo del Servicio Social Estudiantil Obligatorio en Colombia.
<br><br>
HACE CONSTAR
<br><br>
Que el(la) estudiante <strong><?= htmlspecialchars($student_name) ?></strong>, identificado(a) en los registros académicos de esta institución, ha cumplido satisfactoriamente con un total de <strong><?= (int)round($total) ?></strong> horas de Servicio Social Estudiantil Obligatorio, de conformidad con lo establecido en la normatividad vigente.
</p>
<table class="tabla">
  <thead>
    <tr>
      <th>Docente</th>
      <th>Inicio</th>
      <th>Fin</th>
      <th>Estado</th>
      <th>Horas</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($placements as $pl): ?>
    <tr>
      <td><?= htmlspecialchars($pl['teacher']) ?></td>
      <td><?= htmlspecialchars($pl['start_date']) ?></td>
      <td><?= htmlspecialchars($pl['end_date']) ?></td>
      <td><?= htmlspecialchars($pl['status']) ?></td>
      <td><?= (int)round($pl['hours_done']) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<p>
El estudiante desarrolló sus actividades en calidad de apoyo al docente, participando en labores de acompañamiento pedagógico y colaborativo, de acuerdo con el formato institucional de seguimiento y los acompañamientos registrados por el área responsable.
<br><br>
Se firma en la ciudad de Medellín (Antioquia), el día de: <?= date('Y-m-d') ?>.
<br><br>
______________________________<br>
Coordinación Académica
<br><br>
______________________________<br>
Rector(a)
</p>
<?php
$html = ob_get_clean();

// --- Generar PDF ---
$dompdf = new Dompdf();
$dompdf->set_option('isRemoteEnabled', true);
$dompdf->set_option('isHtml5ParserEnabled', true); 
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("constancia_servicio_social.pdf", ["Attachment" => true]);
exit;
