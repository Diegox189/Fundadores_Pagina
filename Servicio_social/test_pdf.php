<?php
require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->loadHtml('<h1>¡Funciona dompdf!</h1><p>Si ves esto en PDF, todo está bien.</p>');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("prueba.pdf", ["Attachment" => false]);