<?php
// ver-certificado.php — vista pública de solo lectura de un certificado
// ya emitido. Sin sesión: el folio es el mismo dato que ya es público
// en el PDF que la persona recibió/descargó, así que no hay nada nuevo
// que exponer acá que esa persona no tuviera ya. NUNCA acepta nombre o
// proyecto como texto libre para renderizar — solo folio exacto contra
// festival_certificados (misma tabla y mismo criterio que ya usa
// verify_festival_certificado), y el resultado se re-arma en el
// servidor a partir de la fila de la BD, nunca eco de lo que mandó el
// visitante.
require_once __DIR__ . '/../../backend/config/database.php';
require_once __DIR__ . '/../../backend/controllers/festivalController.php';

$folio = isset($_GET['folio']) ? trim($_GET['folio']) : '';

$estado = 'sin_folio'; // sin_folio | rate_limited | no_encontrado | ok
$cert = null;

if ($folio !== '') {
    if (!festival_folio_rate_limit_ok($conn)) {
        $estado = 'rate_limited';
    } else {
        $stmt = $conn->prepare("SELECT folio, nombre, proyecto, tipo, fecha FROM festival_certificados WHERE UPPER(folio) = UPPER(?)");
        $stmt->execute([$folio]);
        $cert = $stmt->fetch(PDO::FETCH_ASSOC);
        $estado = $cert ? 'ok' : 'no_encontrado';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Certificado <?= $cert ? htmlspecialchars($cert['folio'], ENT_QUOTES, 'UTF-8') : '' ?> — Festival de las Ciencias y Matemáticas · CEDIC UNET</title>
<meta name="robots" content="noindex">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,700;1,9..40,300;1,9..40,400&family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;1,9..144,300&display=swap" rel="stylesheet">
<script src="../../js/security.js"></script>
<style>
/* Estilos EXCLUSIVOS de esta página: la hoja del certificado en sí
   viene de partials/certificado-render.php. Acá solo la barra
   superior y los estados vacíos (sin folio / no encontrado /
   demasiados intentos) — mismo lenguaje visual "vidrio oscuro" que
   festival-certificado.html/validar-folio.html, ya que este es el
   siguiente paso de ese mismo flujo público. */
.top-strip{
  max-width:1240px;margin:0 auto;padding:18px 22px;
  display:flex;align-items:center;justify-content:space-between;
}
.back-link{
  display:inline-flex;align-items:center;gap:6px;
  text-decoration:none;color:#6b6353;font-size:.85rem;font-weight:600;
  transition:color .2s;
}
.back-link:hover{color:#8a6d3a}

.print-link{
  display:inline-flex;align-items:center;gap:8px;
  background:linear-gradient(180deg,#fff,#f6fafd);color:#3a3428;
  border:1px solid #d8c9a3;border-radius:10px;padding:9px 16px;
  font-size:.85rem;font-weight:700;cursor:pointer;font-family:inherit;
  transition:transform .2s;
}
.print-link:hover{transform:translateY(-1px)}

.empty-wrap{
  min-height:60vh;display:flex;align-items:center;justify-content:center;padding:40px 20px;
}
.empty-card{
  max-width:460px;text-align:center;
  background:rgba(10,37,64,0.92);color:#f3f9ff;
  border:1px solid rgba(190,225,255,0.16);border-radius:20px;
  padding:40px 32px;
}
.empty-card h1{font-family:'Syne',sans-serif;font-size:1.3rem;margin-bottom:12px}
.empty-card p{color:rgba(226,242,255,0.72);font-size:.92rem;line-height:1.6}
.empty-card a{color:#7dd3fc}

@media print{
  .top-strip,.empty-wrap{display:none !important}
}
</style>
<link rel="icon" type="image/png" href="../../assets/logos/evem-icon.png">
</head>
<body>

<header class="top-strip">
  <a href="festival-certificado.html" class="back-link">← Buscar otro certificado</a>
  <?php if ($estado === 'ok'): ?>
  <button class="print-link" onclick="window.print()">Imprimir / Guardar PDF</button>
  <?php endif; ?>
</header>

<?php if ($estado === 'ok'): ?>
  <script>window.CERT_READONLY = true;</script>
  <?php include __DIR__ . '/partials/certificado-render.php'; ?>
  <script>
    // Datos re-armados en el servidor desde festival_certificados — la
    // única parte que varía por folio es nombre/proyecto/tipo/fecha.
    // Título, edición, lugar y firmantes no están en el schema (nunca
    // se guardaron ahí, ver 001_festival_certificados.sql): son los
    // mismos valores fijos que ya usa generar-festival-certificado.php
    // como default para esta edición del festival.
    paint([{
      tipo    : <?= json_encode($cert['tipo']) ?>,
      nombre  : <?= json_encode($cert['nombre']) ?>,
      cedula  : '', // festival_certificados no guarda cédula — nunca se expone acá
      titulo  : 'Festival de las Ciencias. UNET 2026',
      edicion : '',
      fecha   : <?= json_encode($cert['fecha']) ?>,
      lugar   : 'San Cristóbal, 29 de mayo de 2026',
      equipo  : <?= json_encode($cert['proyecto']) ?>,
      folio   : <?= json_encode($cert['folio']) ?>,
      f1n: 'Dr. Gilberto Paredes',   f1c: 'Comité Organizador del Festival · UNET',
      f2n: 'Econ. Óscar Forero',     f2c: 'Presidente · Fundacite Táchira'
    }]);
  </script>
<?php else: ?>
  <div class="empty-wrap">
    <div class="empty-card">
      <?php if ($estado === 'sin_folio'): ?>
        <h1>Falta el folio</h1>
        <p>Esta página necesita un folio en la URL para mostrar un certificado. Buscá el tuyo desde el <a href="festival-certificado.html">buscador de certificados</a>.</p>
      <?php elseif ($estado === 'rate_limited'): ?>
        <h1>Demasiados intentos</h1>
        <p>Esperá unos minutos y volvé a intentar.</p>
      <?php else: ?>
        <h1>Folio no encontrado</h1>
        <p>Verificá que esté bien escrito, o volvé al <a href="festival-certificado.html">buscador de certificados</a> para encontrarlo por tu nombre.</p>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

</body>
</html>
