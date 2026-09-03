<?php
/* ══════════════════════════════════════════════════════════════════
   partials/certificado-render.php — bloque visual del certificado
   (hoja + firma/sello + motor de render), compartido entre:

     - generar-festival-certificado.php (admin, editable, con panel
       de control y modo lote — ese panel NO vive acá, sigue siendo
       propio de esa página)
     - ver-certificado.php (público, solo lectura, una hoja por folio)

   Contrato con el host antes de este include:
     1) Debe haber cargado js/security.js (este parcial usa el
        escapeHTML() global, no define uno propio, para no duplicar
        la misma función en dos archivos).
     2) Opcionalmente puede declarar `window.CERT_READONLY = true;`
        en un <script> ANTES de este include, para que renderSheet()
        quite los contenteditable de la hoja clonada — el generador
        admin no lo declara (o lo declara false) y conserva la edición
        en vivo que ya tenía.

   Expone a quien lo incluya: paint(list) — la única función que un
   host necesita llamar. El admin arma `list` desde su panel/lote; la
   vista pública arma un `list` de un solo elemento con los datos de
   festival_certificados.
   ══════════════════════════════════════════════════════════════════ */
?>
<style>
/* ══════════════════════════════════════════════════════════════════
   CERTIFICADO — Festival de las Ciencias y Matemáticas
   Fraunces + DM Sans, paleta gris-cálido/bronce anclada al marco de
   fondo-festival.jpg (ver comentario en :root). Syne queda solo para
   textos institucionales del membrete.

   El fondo es un <img> (.bg-photo) en el flujo del documento, no
   background-image: así se imprime aunque el navegador tenga
   desactivado "gráficos de fondo". El escudo de UNET y el logo del
   sello son imágenes propias con su azul institucional — no siguen
   la paleta de acá.
   ══════════════════════════════════════════════════════════════════ */

:root{
  /* Paleta anclada al marco de fondo-festival.jpg (gris-cálido +
     bronce/dorado, sin azul) — reemplaza el azul institucional que
     usaba esta hoja antes de tener foto de fondo. Excepción: el
     escudo de UNET y el logo del sello (cedic-Sinfondo.png) son
     imágenes propias con su azul institucional ya "horneado" en el
     píxel — no pasan por estas variables, así que no cambian. */
  --ink:#3a3428;          /* tinta principal */
  --ink-soft:#6b6353;     /* texto secundario */
  --ink-faint:#726a58;    /* metadatos — oscurecido: sobre la foto de
                              fondo (con textura, no un blanco liso)
                              el tono claro original se perdía */
  --accent:#b0894a;
  --accent-deep:#8a6d3a;
  --rule:#d8c9a3;
  --paper:#ffffff;
  --paper-tint:#f6fafd;
  --shell:#eef3f8;
  --panel:#0b2545;
}

*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

body{
  font-family:'DM Sans',system-ui,sans-serif;
  background:var(--shell);
  color:var(--ink);
  -webkit-font-smoothing:antialiased;
}

/* ─────────────────────────  ÁREA DE HOJAS  ──────────────────────── */
.stage{padding:34px 20px 60px}
.sheet-wrap{
  margin:0 auto 30px;
  width:calc(297mm * var(--z,1));
  height:calc(210mm * var(--z,1));
}
.sheet{
  width:297mm;height:210mm;
  transform:scale(var(--z,1));transform-origin:top left;
  position:relative;overflow:hidden;
  background:var(--paper);
  box-shadow:0 30px 60px -30px rgba(11,37,69,.55),0 2px 6px rgba(11,37,69,.1);
}

/* ─── fondo fotográfico ───
   <img>, no background-image: los navegadores desactivan los fondos
   CSS al imprimir por defecto ("gráficos de fondo"), pero un <img> es
   contenido normal de la página y se imprime siempre. */
.bg-photo{
  position:absolute;inset:0;width:100%;height:100%;
  object-fit:cover;z-index:0;display:block;pointer-events:none;
}

/* ─── contenido ─── */
.content{
  position:absolute;inset:0;z-index:1;
  /* La cuerda decorativa del fondo mide ~16mm arriba y ~18.5mm abajo
     (medido sobre fondo-festival.jpg): con el padding anterior
     (16mm/19mm) el membrete y el pie quedaban pegados a la cuerda,
     casi sin margen. Se separan del centro hacia los bordes. */
  padding:24mm 26mm 27mm;
  display:flex;flex-direction:column;align-items:center;text-align:center;
}
/* El cuerpo se centra ópticamente en el hueco entre membrete y firmas:
   así el certificado no queda con el tercio inferior muerto. */
.core{
  flex:1;width:100%;
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding-bottom:4mm;
}

/* Membrete institucional — el UNET es una marca compacta y el de
   Fundacite es apaisado, así que no comparten un mismo height: cada
   uno se dimensiona por su propia proporción para igualar masa visual
   en vez de bounding box. */
.letterhead{width:100%;display:flex;align-items:center;justify-content:center;gap:9mm}
.crest{display:block}
.crest-unet{height:18mm;width:auto}
.crest-fundacite{width:38mm;height:auto;max-height:14mm;object-fit:contain}
.crest.cedic{filter:none}
.letterhead-text{line-height:1.5}
.letterhead-text .l1{
  font-size:6.4pt;font-weight:700;letter-spacing:.19em;text-transform:uppercase;color:var(--ink-soft);
}
.letterhead-text .l2{
  font-family:'Syne',sans-serif;font-weight:700;font-size:9.6pt;letter-spacing:.03em;
  text-transform:uppercase;color:var(--ink);
}
.letterhead-text .l3{
  font-size:6.4pt;font-weight:500;letter-spacing:.15em;text-transform:uppercase;color:var(--ink-faint);
}

.eyebrow{
  margin-top:0;
  /* Oculto a pedido, sin sacarlo de TIPOS ni del render: set() en
     renderSheet() limpia cualquier display inline a '' cada vez que
     repinta (el texto del eyebrow nunca está vacío), así que un
     display:none inline se pisaría solo en el próximo repaint.
     !important en la clase es lo único que sobrevive a eso. */
  display:none !important;
  align-items:center;gap:5mm;
  font-size:7.6pt;font-weight:700;letter-spacing:.34em;text-transform:uppercase;
  color:var(--accent-deep);
}
.eyebrow::before,.eyebrow::after{
  content:'';height:.35mm;width:16mm;
  background:linear-gradient(90deg,transparent,var(--rule));
}
.eyebrow::after{background:linear-gradient(90deg,var(--rule),transparent)}

.fest-title{
  margin-top:3.5mm;
  font-family:'Fraunces',Georgia,serif;font-weight:600;
  font-size:26pt;line-height:1.1;letter-spacing:-.01em;
  color:var(--ink);
  max-width:100%;
}
.fest-edition{
  margin-top:2.2mm;
  font-size:7.6pt;font-weight:500;letter-spacing:.2em;text-transform:uppercase;
  color:var(--accent-deep);
}

.grants{
  margin-top:7mm;
  font-family:'Fraunces',Georgia,serif;font-style:italic;font-weight:300;
  font-size:11pt;color:var(--ink-soft);letter-spacing:.02em;
}

.laureate{
  margin-top:2mm;
  white-space:nowrap;
  font-family:'Fraunces',Georgia,serif;font-weight:600;
  font-size:36pt;line-height:1.1;letter-spacing:-.015em;
  color:var(--ink);
  max-width:100%;
}
.laureate-rule{
  margin-top:3mm;width:96mm;height:.4mm;
  background:linear-gradient(90deg,transparent,var(--rule) 22%,var(--accent) 50%,var(--rule) 78%,transparent);
}
.laureate-id{
  margin-top:2.2mm;
  font-size:7.8pt;font-weight:500;letter-spacing:.16em;color:var(--ink-faint);
}

.citation{
  margin-top:5.5mm;max-width:220mm;
  font-size:10pt;font-weight:400;line-height:1.72;color:var(--ink-soft);
}
.citation strong{font-weight:600;color:var(--ink)}

.place-date{
  margin-top:4.5mm;
  font-size:8.6pt;font-weight:400;color:var(--ink-faint);letter-spacing:.02em;
}

/* ─── pie: firmas + sello ─── */
/* El marco nuevo (marco_festival.jpeg) tiene esquinas curvas que
   entran más hacia el centro que un borde recto: a la altura del pie
   (~180mm) la curva ya está a ~21mm del canto. Con el padding de
   .content (26mm) el texto quedaba pegado a esa curva — folio,
   verificación y el cargo de la firma derecha. 14mm extra acá (40mm
   en total) lo despega sin tocar el ancho de .fest-title/.laureate,
   que sí necesitan todo el aire posible para el autofit. */
.footer{width:100%;padding:0 14mm}
.signatures{
  display:grid;grid-template-columns:1fr auto 1fr;align-items:end;gap:15mm;width:100%;
}
.meta-row{
  margin-top:5mm;padding-top:2.6mm;
  border-top:.2mm solid var(--rule);
  display:flex;justify-content:space-between;align-items:center;
  font-size:6.6pt;font-weight:500;letter-spacing:.14em;color:var(--ink-faint);
  text-transform:uppercase;
}
.sign{display:flex;flex-direction:column;align-items:center;gap:1.6mm}
.sign-line{width:62mm;height:.35mm;background:var(--ink);opacity:.45}
.sign-name{
  font-family:'Syne',sans-serif;font-weight:700;font-size:9pt;letter-spacing:-.005em;color:var(--ink);
}
.sign-role{font-size:7.2pt;font-weight:400;color:var(--ink-faint);letter-spacing:.06em;line-height:1.45}

.seal{width:46mm;height:46mm;margin:0 auto}
.seal-logo{width:100%;height:100%;object-fit:contain;opacity:.9;display:block}

/* ─── firmas escaneadas + sello de Fundacite ───
   .sign-slot/.sign-firma son genéricos: los usan ambas firmas (Gilberto
   Paredes y Óscar Forero) para que "se apoyen" sobre su .sign-line con
   el mismo tratamiento. height fija en .sign-firma (no width): cada
   imagen tiene su propia proporción natural (la de Forero es más
   grande en px que la de Gilberto), así que fijar el ancho haría que
   una se vea más grande que la otra en el papel — con height fija y
   width:auto ambas firmas terminan con la misma altura impresa aunque
   partan de resoluciones distintas.
   El sello sigue siendo exclusivo de Forero: .sign-signed (modificador,
   no toca .sign a secas) crea el positioning context para anclar
   .sign-sello fuera del flujo y superponerlo sobre línea+nombre sin
   empujar el layout del resto del pie; rotado para leer como un timbre
   estampado a mano, no un logo pegado prolijo. */
.sign-signed{position:relative}
.sign-slot{width:62mm;height:16mm;position:relative}
.sign-firma{
  position:absolute;left:50%;bottom:-2mm;transform:translateX(-50%);
  height:15mm;width:auto;object-fit:contain;
  pointer-events:none;
}
.sign-sello{
  position:absolute;right:-3mm;bottom:5mm;z-index:2;
  width:26mm;height:auto;object-fit:contain;
  transform:rotate(-11deg);
  opacity:.82;
  pointer-events:none;
}

/* campos retocables directamente sobre la hoja — solo tiene efecto
   donde el host no haya quitado contenteditable (ver CERT_READONLY
   en el script de más abajo) */
[contenteditable]{outline:none;border-radius:1mm}
[contenteditable]:focus{
  box-shadow:0 0 0 .6mm rgba(14,165,233,.22);
  background:rgba(14,165,233,.05);
}

/* ─────────────────────────  IMPRESIÓN  ──────────────────────────── */
@page{size:A4 landscape;margin:0}

@media print{
  :root{--z:1 !important}
  body{background:#fff}
  /* .panel/.stage-hint son del panel de control del generador admin,
     no de este parcial — la regla no rompe nada si no existen. */
  .panel,.stage-hint{display:none !important}
  .stage{padding:0}
  .sheet-wrap{
    width:297mm !important;height:210mm !important;margin:0 !important;
    break-after:page;page-break-after:always;
  }
  .sheet-wrap:last-child{break-after:auto;page-break-after:auto}
  .sheet{
    transform:none !important;box-shadow:none !important;
    -webkit-print-color-adjust:exact;print-color-adjust:exact;
  }
  [contenteditable]:focus{box-shadow:none;background:none}
}

@media (max-width:760px){
  .stage{padding:20px 10px 40px}
}
</style>

<div class="stage" id="stage"></div>

<!-- ══════════════════ PLANTILLA DE HOJA ══════════════════ -->
<template id="tpl">
<div class="sheet-wrap">
  <div class="sheet">

    <img class="bg-photo" src="../../assets/certificados/fondo-festival.jpg" alt="" onerror="this.style.display='none'">

    <div class="content">

      <div class="letterhead">
        <img class="crest crest-unet" src="../../assets/logos/unet-logo.png" alt="" onerror="this.style.display='none'">
        <div class="letterhead-text">
          <div class="l1">República Bolivariana de Venezuela</div>
          <div class="l2">Universidad Nacional Experimental del Táchira</div>
          <div class="l3">Centro de Enseñanza y Divulgación de las Ciencias · CEDIC</div>
        </div>
        <img class="crest crest-fundacite" src="../../assets/logos/logo_Fundacite-certificado.png" alt="" onerror="this.style.display='none'">
      </div>

      <div class="core">
      <div class="eyebrow" data-f="eyebrow" contenteditable="true">Certificado de participación</div>

      <h2 class="fest-title" data-f="titulo" contenteditable="true">Festival de las Ciencias y Matemáticas</h2>
      <div class="fest-edition" data-f="edicion" contenteditable="true"></div>

      <p class="grants" data-f="grants">Otorgado a</p>
      <p class="laureate" data-f="nombre" contenteditable="true">María Alejandra Contreras Rangel</p>
      <div class="laureate-rule"></div>
      <div class="laureate-id" data-f="cedula" contenteditable="true">V-28.412.905</div>

      <p class="citation" data-f="cita" contenteditable="true"></p>

      <p class="place-date" data-f="lugar" contenteditable="true">San Cristóbal, 29 de mayo de 2026</p>
      </div>

      <div class="footer">
        <div class="signatures">
        <div class="sign">
          <div class="sign-slot">
            <img class="sign-firma" src="../../assets/certificados/firma-gilberto-paredes.png" alt="" onerror="this.style.display='none'">
          </div>
          <div class="sign-line"></div>
          <div class="sign-name" data-f="f1n" contenteditable="true">Dr. Gilberto Paredes</div>
          <div class="sign-role" data-f="f1c" contenteditable="true">Comité Organizador del Festival · UNET</div>
        </div>

        <div class="seal">
          <img class="seal-logo" src="../../assets/logos/cedic-Sinfondo.png" alt="" onerror="this.style.display='none'">
        </div>

        <div class="sign sign-signed">
          <div class="sign-slot">
            <img class="sign-firma" src="../../assets/certificados/firma-oscar-forero.png" alt="" onerror="this.style.display='none'">
          </div>
          <div class="sign-line"></div>
          <img class="sign-sello" src="../../assets/certificados/sello-fundacite.png" alt="" onerror="this.style.display='none'">
          <div class="sign-name" data-f="f2n" contenteditable="true">Econ. Óscar Forero</div>
          <div class="sign-role" data-f="f2c" contenteditable="true">Presidente · Fundacite Táchira</div>
        </div>
        </div>

        <div class="meta-row">
          <span data-f="folio">Folio FCM-2026-0147</span>
          <span>Verificable en evem.unet.edu.ve/pages/festival/validar-folio.html</span>
        </div>
      </div>
    </div>
  </div>
</div>
</template>

<script>
/* ══════════════════════════════════════════════════════════════════
   Motor de render compartido: una sola función pinta una hoja a
   partir de un objeto de datos. paint(list) es lo único que un host
   necesita llamar — el admin arma `list` desde su panel/lote propio,
   ver-certificado.php arma un `list` de un solo elemento con los
   datos de festival_certificados.
   ══════════════════════════════════════════════════════════════════ */

const TIPOS = {
  participacion:{
    eyebrow:'Certificado de participación',
    grants:'Otorgado a',
    cita:d=>`Por su valiosa participación en el <strong>${d.titulo}</strong>${d.edicionClause}celebrado en las instalaciones de la Universidad Nacional Experimental del Táchira los días ${d.fecha}. Su curiosidad y compromiso sostienen el espíritu de esta celebración de la ciencia.`
  },
  ponente:{
    eyebrow:'Certificado de ponencia',
    grants:'Otorgado a',
    cita:d=>`Por su destacada intervención como <strong>ponente</strong> en el ${d.titulo}${d.edicionClause}celebrado en la Universidad Nacional Experimental del Táchira los días ${d.fecha}. Su aporte enriqueció el programa académico del festival.`
  },
  expositor:{
    eyebrow:'Certificado de exposición',
    grants:'Otorgado a',
    cita:d=>`Por la presentación de su proyecto en el Expo-Festival del <strong>${d.titulo}</strong>${d.edicionClause}realizado en la Universidad Nacional Experimental del Táchira los días ${d.fecha}. El trabajo expuesto cumplió con los criterios de rigor y divulgación establecidos por el comité.`
  },
  ganador:{
    eyebrow:'Mención honorífica',
    grants:'Distinción conferida a',
    cita:d=>`Por la calidad excepcional del trabajo presentado en el <strong>${d.titulo}</strong>${d.edicionClause}celebrado los días ${d.fecha}. El comité evaluador acordó otorgar esta distinción por unanimidad.`
  },
  organizador:{
    eyebrow:'Certificado de servicio',
    grants:'Otorgado a',
    cita:d=>`Por su dedicación como integrante del <strong>comité organizador</strong> del ${d.titulo}${d.edicionClause}celebrado en la Universidad Nacional Experimental del Táchira los días ${d.fecha}. Este festival existió gracias a su trabajo.`
  },
  asistente:{
    eyebrow:'Constancia de asistencia',
    grants:'Se hace constar que',
    cita:d=>`Asistió al <strong>${d.titulo}</strong>${d.edicionClause}celebrado en la Universidad Nacional Experimental del Táchira los días ${d.fecha}, cumpliendo con la totalidad de las actividades del programa.`
  },
  tutor:{
    eyebrow:'Certificado de participación',
    grants:'Otorgado a',
    cita:d=>`Por su participación como Tutora del proyecto <strong>${d.equipo}</strong> en el <strong>${d.titulo}</strong>, celebrado en las instalaciones de la Universidad Nacional Experimental del Táchira los días ${d.fecha}. Su participación y compromiso para guiar a sus estudiantes contribuyen al fortalecimiento de la ciencia y la tecnología.`
  }
};

const $ = s => document.querySelector(s);
const stage = $('#stage'), tpl = $('#tpl');

function renderSheet(d){
  const node = tpl.content.cloneNode(true);
  const t = TIPOS[d.tipo] || TIPOS.participacion;
  const set = (f,v,html)=>{
    const el = node.querySelector(`[data-f="${f}"]`);
    if(!el) return;
    if(html) el.innerHTML = v; else el.textContent = v;
    el.style.display = v ? '' : 'none';
  };
  // edicionClause en un solo punto: si "Edición" viene vacío, la cita no
  // debe quedar con una coma suelta ("...UNET 2026, , celebrado...").
  d.edicionClause = d.edicion ? `, ${d.edicion}, ` : ', ';
  // El proyecto/equipo no tiene celda propia en el diseño: se suma como
  // una línea más dentro de la cita, igual para cualquier tipo.
  let cita = t.cita(d);
  if(d.equipo) cita += ` <strong>Proyecto presentado</strong>: &ldquo;${escapeHTML(d.equipo)}&rdquo;.`;
  set('eyebrow',t.eyebrow);
  set('grants',t.grants);
  set('titulo',d.titulo);
  set('edicion',d.edicion);
  set('nombre',d.nombre);
  set('cedula',d.cedula);
  set('cita',cita,true);
  set('lugar',d.lugar);
  set('folio',d.folio ? 'Folio '+d.folio : '');
  set('f1n',d.f1n); set('f1c',d.f1c);
  set('f2n',d.f2n); set('f2c',d.f2c);

  // Vista pública de solo lectura: el host declara esto ANTES de este
  // include. Sin esto, la hoja quedaría "editable" en el navegador de
  // cualquier visitante aunque nada de eso se guarde en ningún lado —
  // confuso igual, así que se desactiva del todo.
  if (window.CERT_READONLY) {
    node.querySelectorAll('[contenteditable]').forEach(el => el.removeAttribute('contenteditable'));
  }

  return node;
}

function paint(list){
  stage.innerHTML='';
  list.forEach(d => stage.appendChild(renderSheet(d)));
  document.querySelectorAll('.sheet').forEach(sh=>{
    autofit(sh.querySelector('.laureate'),36,20);
  });
  fit();
}

/* Nombres y títulos largos: se reduce el cuerpo hasta que la línea
   entra en la caja. Sin esto, un nombre de cinco apellidos rompe la
   composición y hay que corregir a mano cada certificado. */
function autofit(el,maxPt,minPt){
  if(!el) return;
  let pt = maxPt;
  el.style.fontSize = pt+'pt';
  const room = el.parentElement.clientWidth;
  let guard = 0;
  while(el.scrollWidth > room && pt > minPt && guard++ < 60){
    pt -= 0.5;
    el.style.fontSize = pt+'pt';
  }
}

/* Ajuste a pantalla: la hoja mide 297mm reales; se escala para caber
   en el ancho disponible y el contenedor toma la altura resultante. */
const MM = 297/25.4*96;
function fit(){
  const avail = stage.clientWidth - 8;
  const z = Math.min(1, avail/MM);
  document.querySelectorAll('.sheet-wrap').forEach(w=>w.style.setProperty('--z',z));
}
window.addEventListener('resize',fit);
</script>
