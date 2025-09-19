<?php
// helpers.php
function h($str) { return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8'); }

function parse_days(array $input): array {
  // Sanitiza y mantiene solo días válidos
  $valid = ['Lun','Mar','Mie','Jue','Vie','Sab','Dom'];
  return array_values(array_intersect($valid, $input));
}

function hours_between($start, $end): float {
  $s = strtotime($start);
  $e = strtotime($end);
  if ($e <= $s) return 0.0;
  $mins = ($e - $s) / 60;
  return round($mins / 60, 2);
}
