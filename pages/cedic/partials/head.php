<?php
/* ══════════════════════════════════════════════════════════════════
   partials/head.php — apertura del documento

   Cada página define estas variables ANTES de incluir este archivo:

     $titulo       (string)  <title> de la página
     $descripcion  (string)  meta description
     $pagina       (string)  clave de la página activa en el navbar:
                             'inicio' | 'actividades' | 'formacion' | 'contenidos'

   Ejemplo de uso:
     <?php
       $titulo      = 'Actividades y encuentros — CEDIC';
       $descripcion = 'Galería de olimpiadas, Encomat y astronomía.';
       $pagina      = 'actividades';
       include 'partials/head.php';
     ?>
   ══════════════════════════════════════════════════════════════════ */

require __DIR__ . '/config.php';

$titulo      = $titulo      ?? 'CEDIC — Centro de Enseñanza y Divulgación de las Ciencias | UNET';
$descripcion = $descripcion ?? 'El CEDIC es un centro dedicado a la formación y actualización de profesionales en la didáctica de la matemática, la física y la química. Departamento de Matemática y Física, UNET.';
$pagina      = $pagina      ?? 'inicio';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/reset.css">
    <link rel="stylesheet" href="../../css/cedic.css">
</head>
<body>

<?php include __DIR__ . '/sprite.php'; ?>
<?php include __DIR__ . '/nav.php'; ?>
