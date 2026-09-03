<?php
// Página que emite certificados oficiales del Festival: exige la misma
// sesión de admin del panel 'festival' que usa el API (ver
// backend/config/auth.php). Sin sesión válida, redirige al login del
// panel en vez de mostrar el 401/403 en JSON crudo que usa el API.
require_once __DIR__ . '/../../backend/config/auth.php';
bootstrap_admin_session();
require_admin_session('festival', '../admin/admin-festival.html');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Certificado — Festival de las Ciencias y Matemáticas · CEDIC UNET</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,700;1,9..40,300;1,9..40,400&family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;1,9..144,300&display=swap" rel="stylesheet">
<script src="../../js/security.js"></script>
<style>
/* ══════════════════════════════════════════════════════════════════
   Estilos EXCLUSIVOS del panel de control admin (el generador). El
   diseño del certificado en sí — hoja, firma, sello, tipografía —
   vive en partials/certificado-render.php, compartido con
   ver-certificado.php (vista pública de solo lectura). Acá solo
   queda lo que jamás aparece fuera de esta herramienta.
   ══════════════════════════════════════════════════════════════════ */
.panel{
  position:sticky; top:0; z-index:50;
  background:linear-gradient(180deg,#0d2b52,#081b34);
  color:#e7f1fb;
  border-bottom:1px solid rgba(148,196,240,.22);
  box-shadow:0 14px 34px -22px rgba(0,0,0,.9);
}
.panel-inner{max-width:1240px;margin:0 auto;padding:18px 22px}

.panel-head{
  display:flex;align-items:baseline;gap:14px;flex-wrap:wrap;
  margin-bottom:16px;
}
.panel-head h1{
  font-family:'Syne',sans-serif;font-weight:800;font-size:1.02rem;letter-spacing:-.01em;
}
.panel-head p{font-size:.78rem;color:#9fc2e4;font-weight:300}

.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:12px 16px}

.field{display:flex;flex-direction:column;gap:5px}
.field label{
  font-size:.63rem;font-weight:700;letter-spacing:.13em;text-transform:uppercase;
  color:#8fb6dc;
}
.field input,.field select,.field textarea{
  font-family:inherit;font-size:.85rem;color:#f2f8ff;
  background:rgba(226,242,255,.09);
  border:1px solid rgba(148,196,240,.24);
  border-radius:9px;padding:9px 11px;
  transition:border-color .2s,background .2s;
}
.field input:focus,.field select:focus,.field textarea:focus{
  outline:none;border-color:#b0894a;background:rgba(226,242,255,.15);
}
.field select option{background:#0d2b52;color:#f2f8ff}
.field textarea{resize:vertical;min-height:88px;line-height:1.55;font-size:.82rem}
.field .hint{font-size:.68rem;color:#7ba3cc;font-weight:300;line-height:1.45}

.panel-actions{
  display:flex;gap:10px;flex-wrap:wrap;align-items:center;
  margin-top:16px;padding-top:16px;border-top:1px solid rgba(148,196,240,.16);
}

.btn{
  font-family:inherit;font-size:.82rem;font-weight:600;
  padding:10px 20px;border-radius:10px;border:1px solid transparent;
  cursor:pointer;transition:transform .2s cubic-bezier(.16,1,.3,1),box-shadow .2s,background .2s;
  display:inline-flex;align-items:center;gap:8px;
}
.btn:hover{transform:translateY(-1.5px)}
.btn-solid{background:linear-gradient(180deg,#fff,#dbeafe);color:#06304f;
  box-shadow:inset 0 1px 0 rgba(255,255,255,.9),0 10px 24px -12px rgba(56,189,248,.9)}
.btn-line{background:rgba(226,242,255,.08);border-color:rgba(148,196,240,.3);color:#e7f1fb}
.btn-line:hover{border-color:#b0894a}
.panel-note{font-size:.72rem;color:#8fb6dc;font-weight:300;margin-left:auto;max-width:340px;line-height:1.5}

.batch-wrap{display:none;margin-top:14px}
.batch-wrap.on{display:block}

.stage-hint{
  max-width:1240px;margin:0 auto 22px;padding:0 22px;
  font-size:.78rem;color:#4f7095;font-weight:300;line-height:1.6;
}
.stage-hint code{
  background:#dce8f4;padding:1px 6px;border-radius:5px;font-size:.74rem;color:#0b2545;
}

@media print{
  .panel,.stage-hint{display:none !important}
}

@media (max-width:760px){
  .panel-inner{padding:14px}
  .panel-note{margin-left:0}
}
</style>
<link rel="icon" type="image/png" href="../../assets/logos/evem-icon.png">
</head>
<body>

<!-- ══════════════════ PANEL (no se imprime) ══════════════════ -->
<div class="panel">
  <div class="panel-inner">
    <div class="panel-head">
      <h1>Generador de certificados</h1>
      <p>Festival de las Ciencias y Matemáticas · CEDIC UNET</p>
    </div>

    <div class="grid">
      <div class="field">
        <label for="f-tipo">Tipo de reconocimiento</label>
        <select id="f-tipo">
          <option value="participacion">Participación</option>
          <option value="ponente">Ponente</option>
          <option value="expositor">Expositor de proyecto</option>
          <option value="ganador">Mención honorífica</option>
          <option value="organizador">Comité organizador</option>
          <option value="asistente">Asistencia</option>
          <option value="tutor">Tutor de proyecto</option>
        </select>
      </div>

      <div class="field">
        <label for="f-nombre">Nombre del laureado</label>
        <input id="f-nombre" type="text" value="María Alejandra Contreras Rangel" autocomplete="off">
      </div>

      <div class="field">
        <label for="f-cedula">Cédula (opcional)</label>
        <input id="f-cedula" type="text" value="V-28.412.905" autocomplete="off">
      </div>

      <div class="field">
        <label for="f-titulo">Título del festival</label>
        <input id="f-titulo" type="text" value="Festival de las Ciencias. UNET 2026">
      </div>

      <div class="field">
        <label for="f-edicion">Edición</label>
        <input id="f-edicion" type="text" value="">
      </div>

      <div class="field">
        <label for="f-fecha">Fecha del evento</label>
        <input id="f-fecha" type="text" value="28 y 29 de mayo de 2026">
      </div>

      <div class="field">
        <label for="f-lugar">Lugar y fecha de emisión</label>
        <input id="f-lugar" type="text" value="San Cristóbal, 29 de mayo de 2026">
      </div>

      <div class="field">
        <label for="f-folio">Prefijo de folio</label>
        <input id="f-folio" type="text" value="FCM-2026">
        <span class="hint">Cada hoja recibe un sufijo aleatorio propio (crypto.getRandomValues) — no correlativo, no adivinable.</span>
      </div>

      <div class="field">
        <label for="f-f1n">Firma 1 · nombre</label>
        <input id="f-f1n" type="text" value="Dr. Gilberto Paredes">
      </div>
      <div class="field">
        <label for="f-f1c">Firma 1 · cargo</label>
        <input id="f-f1c" type="text" value="Comité Organizador del Festival · UNET">
      </div>
      <div class="field">
        <label for="f-f2n">Firma 2 · nombre</label>
        <input id="f-f2n" type="text" value="Econ. Óscar Forero">
      </div>
      <div class="field">
        <label for="f-f2c">Firma 2 · cargo</label>
        <input id="f-f2c" type="text" value="Presidente · Fundacite Táchira">
      </div>
    </div>

    <div class="batch-wrap" id="batch">
      <div class="field" style="margin-top:6px">
        <label for="f-json">JSON de participantes</label>
        <textarea id="f-json" spellcheck="false">[
  { "nombre": "María Alejandra Contreras Rangel", "cedula": "V-28.412.905", "tipo": "participacion", "equipo": null },
  { "nombre": "Jesús Eduardo Moncada Peña", "cedula": "V-27.980.114", "tipo": "expositor", "equipo": "Sistema de purificación solar" },
  { "nombre": "Andreína Sofía Villamizar", "cedula": "V-29.104.663", "tipo": "ponente", "equipo": null },
  { "nombre": "Carlos Enrique Duque Ramírez", "cedula": "V-26.771.508", "tipo": "asistente", "equipo": null }
]</textarea>
        <span class="hint">
          Array de objetos <code>{nombre, cedula, tipo, equipo}</code>. <code>tipo</code> acepta
          <code>participacion</code> · <code>ponente</code> · <code>expositor</code> · <code>ganador</code> ·
          <code>organizador</code> · <code>asistente</code> — si falta o no coincide con ninguno, se usa
          <code>participacion</code>. El folio de cada fila es aleatorio, no depende del orden del JSON.
        </span>
        <input type="file" id="f-json-file" accept="application/json,.json" style="margin-top:8px">
        <span id="jsonError" class="hint" style="display:none;color:#dc2626;font-weight:600"></span>
      </div>
    </div>

    <div class="panel-actions">
      <button class="btn btn-solid" id="btn-print">Imprimir / Guardar PDF</button>
      <button class="btn btn-line" id="btn-batch">Modo lote</button>
      <button class="btn btn-line" id="btn-gen" style="display:none">Generar lote</button>
      <button class="btn btn-line" id="btn-sync" style="display:none">Sincronizar folios</button>
      <p class="panel-note">Al imprimir, elegí <strong>A4 horizontal</strong>, márgenes <strong>ninguno</strong> y activá <strong>gráficos de fondo</strong> si tu navegador lo ofrece.</p>
      <p id="syncStatus" class="hint" style="width:100%;display:none"></p>
    </div>
  </div>
</div>

<p class="stage-hint">
  Cualquier texto de la hoja se puede corregir haciendo clic directamente sobre él antes de imprimir.
  Para producir toda la tanda de una vez, usá <code>Modo lote</code>, pegá la lista y generá: sale un PDF con una página por participante.
</p>

<?php include __DIR__ . '/partials/certificado-render.php'; ?>

<script>
/* ══════════════════════════════════════════════════════════════════
   Lógica EXCLUSIVA del generador admin: leer el panel, generar
   folios, armar el lote y sincronizar. paint()/TIPOS/renderSheet/$
   ya existen acá arriba — los define el <script> del parcial recién
   incluido, en el mismo scope global (misma página, sin type=module).
   ══════════════════════════════════════════════════════════════════ */

function readForm(){
  return {
    tipo:$('#f-tipo').value,
    nombre:$('#f-nombre').value,
    cedula:$('#f-cedula').value,
    titulo:$('#f-titulo').value,
    edicion:$('#f-edicion').value,
    fecha:$('#f-fecha').value,
    lugar:$('#f-lugar').value,
    folioPrefix:$('#f-folio').value,
    f1n:$('#f-f1n').value, f1c:$('#f-f1c').value,
    f2n:$('#f-f2n').value, f2c:$('#f-f2c').value
  };
}

/* Folio no adivinable: sufijo aleatorio de crypto.getRandomValues, no
   un correlativo. Alfabeto sin 0/O/1/I/L — son los caracteres que más
   se confunden al transcribir un folio a mano en la mesa de verificación. */
const FOLIO_ALPHABET = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';
function randomFolio(prefix){
  const bytes = new Uint8Array(8);
  crypto.getRandomValues(bytes);
  let suffix = '';
  for(let i=0;i<bytes.length;i++) suffix += FOLIO_ALPHABET[bytes[i] % FOLIO_ALPHABET.length];
  return `${prefix}-${suffix}`;
}

/* Se genera una vez y se cachea: si regenerara el folio en cada
   repintado (cada tecla en el panel dispara paint()), la hoja de
   vista previa mostraría un folio distinto todo el tiempo. */
let singleFolio = null;
function single(){
  const base = readForm();
  if(!singleFolio) singleFolio = randomFolio(base.folioPrefix);
  paint([Object.assign({},base,{folio:singleFolio})]);
}

/* Único punto de traducción tipo→categoría: si el backend algún día manda
   otro vocabulario, se ajusta acá y no en cada llamada. Desconocido o
   ausente cae en 'participacion'. */
const TIPOS_VALIDOS = Object.keys(TIPOS);
function normalizarTipo(raw){
  const t = String(raw ?? '').trim().toLowerCase();
  return TIPOS_VALIDOS.includes(t) ? t : 'participacion';
}

function mostrarErrorJSON(msg){
  const el = $('#jsonError');
  el.textContent = msg;
  el.style.display = msg ? '' : 'none';
}

let batchFolios = null, batchFoliosKey = null, lastBatchRows = null;
function batch(){
  const base = readForm();
  const raw = $('#f-json').value.trim();
  if(!raw){ mostrarErrorJSON(''); batchFolios=null; batchFoliosKey=null; lastBatchRows=null; single(); return; }

  let lista;
  try{
    lista = JSON.parse(raw);
  }catch(e){
    mostrarErrorJSON('JSON inválido: ' + e.message);
    return;
  }
  if(!Array.isArray(lista)){
    mostrarErrorJSON('El JSON debe ser un array de participantes.');
    return;
  }
  if(!lista.length){ mostrarErrorJSON(''); batchFolios=null; batchFoliosKey=null; lastBatchRows=null; single(); return; }

  mostrarErrorJSON('');

  // Folios duplicados dentro del mismo JSON: sync_festival_certificados
  // hace UPSERT por folio, así que dos filas con el mismo folio NO
  // fallarían con un error — la segunda pisaría en silencio los datos
  // de la primera ya guardados en festival_certificados. Se corta acá,
  // antes de generar nada, en vez de dejar que se sincronice así.
  const foliosExplicitos = lista.map(p => String(p.folio ?? '').trim()).filter(f => f !== '');
  const vistos = new Set();
  const repetidos = new Set();
  foliosExplicitos.forEach(f => {
    if(vistos.has(f)) repetidos.add(f);
    vistos.add(f);
  });
  if(repetidos.size){
    mostrarErrorJSON('Folios duplicados en el JSON: ' + Array.from(repetidos).join(', ') + '. Corregí antes de generar.');
    return;
  }

  // Los folios se asignan una sola vez por lote y se cachean por el
  // contenido exacto del JSON + prefijo: si el admin solo retoca el
  // título o una firma, paint() se vuelve a llamar pero NO se barajan
  // folios nuevos — importa porque estos son los que se sincronizan al
  // backend, no querés que cambien bajo el pie entre un tecleo y otro.
  // Folio explícito por fila (p.folio): se respeta tal cual llega —
  // crítico quando el lote corresponde a certificados YA impresos, con
  // folio ya fijado en el PDF real. Sin p.folio, se genera uno nuevo
  // como siempre.
  const cacheKey = base.folioPrefix + ' ' + raw;
  if(cacheKey !== batchFoliosKey || !batchFolios || batchFolios.length !== lista.length){
    batchFolios = lista.map(p => (p.folio && String(p.folio).trim()) || randomFolio(base.folioPrefix));
    batchFoliosKey = cacheKey;
  }

  lastBatchRows = lista.map((p,i) => Object.assign({},base,{
    nombre : p.nombre  ?? '',
    cedula : p.cedula  ?? '',
    tipo   : normalizarTipo(p.tipo),
    equipo : p.equipo || null,
    folio  : batchFolios[i]
  }));
  paint(lastBatchRows);
}

let batchOn=false;
$('#btn-batch').addEventListener('click',()=>{
  batchOn=!batchOn;
  $('#batch').classList.toggle('on',batchOn);
  $('#btn-gen').style.display = batchOn?'inline-flex':'none';
  $('#btn-sync').style.display = batchOn?'inline-flex':'none';
  $('#btn-batch').textContent = batchOn?'Volver a hoja única':'Modo lote';
  batchOn?batch():single();
});
$('#btn-gen').addEventListener('click',batch);
$('#btn-print').addEventListener('click',()=>window.print());

$('#f-json-file').addEventListener('change', async (e)=>{
  const file = e.target.files[0];
  if(!file) return;
  $('#f-json').value = await file.text();
  batchOn = true;
  $('#batch').classList.add('on');
  $('#btn-gen').style.display = 'inline-flex';
  $('#btn-sync').style.display = 'inline-flex';
  $('#btn-batch').textContent = 'Volver a hoja única';
  batch();
});

// Manda el lote ya renderizado (folios ya asignados) al backend. Requiere
// sesión de admin del panel Festival activa en el mismo navegador — el
// endpoint responde 401 si no. El upsert por folio en el servidor hace
// que reintentar tras un error de red sea seguro: no duplica filas.
$('#btn-sync').addEventListener('click', async () => {
  if(!lastBatchRows || !lastBatchRows.length) return;
  const btn = $('#btn-sync');
  const status = $('#syncStatus');
  const original = btn.textContent;
  btn.disabled = true;
  btn.textContent = 'Sincronizando…';
  status.style.display = 'none';

  try{
    const payload = lastBatchRows.map(d => ({
      folio: d.folio, nombre: d.nombre, proyecto: d.equipo || null, tipo: d.tipo, fecha: d.fecha
    }));
    const res = await fetch('../../backend/api.php?action=sync_festival_certificados', {
      method: 'POST',
      credentials: 'include',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(payload)
    });
    const out = await res.json();
    if(!res.ok) throw new Error(out.error || `Error ${res.status} al sincronizar.`);

    status.style.color = out.errores.length ? '#b45309' : '#059669';
    status.textContent = `Sincronizados ${out.guardados}/${out.total}.` +
      (out.errores.length ? ` ${out.errores.length} con error — revisá la consola.` : '');
    if(out.errores.length) console.warn('[sync_festival_certificados] errores:', out.errores);
  }catch(err){
    status.style.color = '#dc2626';
    status.textContent = 'Error al sincronizar: ' + err.message;
  }finally{
    status.style.display = '';
    btn.disabled = false;
    btn.textContent = original;
  }
});

document.querySelectorAll('.panel input,.panel select').forEach(el=>{
  el.addEventListener('input',()=>{ batchOn?batch():single(); });
});
$('#f-json').addEventListener('input',()=>{ if(batchOn) batch(); });

single();
</script>
</body>
</html>
