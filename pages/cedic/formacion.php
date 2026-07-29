<?php
/* ══════════════════════════════════════════════════════════════════
   formacion.php — CEDIC, UNET
   El chrome vive en partials/; acá solo el contenido de la página.
   ══════════════════════════════════════════════════════════════════ */

$titulo      = 'Cursos, talleres y diplomados — CEDIC | UNET';
$descripcion = 'Oferta formativa del CEDIC: cursos cortos para estudiantes, talleres para docentes y diplomados en didáctica de las ciencias. Metodologías STEAM y Feynman.';
$pagina      = 'formacion';

include 'partials/head.php';
?>
    <!-- ═══════════════════ CABECERA ═══════════════════ -->
    <section class="page-hero">
        <div class="wrap page-hero-inner">
                <nav class="breadcrumb" aria-label="Ruta de navegación">
                    <a href="../../index.html">EVEM</a>
                    <span class="sep">/</span>
                    <a href="cedic.php">CEDIC</a>
                    <span class="sep">/</span>
                    <span aria-current="page">Cursos, talleres y diplomados</span>
                </nav>
                <h1>Cursos, talleres y diplomados</h1>
                <p>Empoderamos a estudiantes y profesores con una inmersión profunda, intensiva y práctica en diversas áreas de la ciencia, a través de las metodologías STEAM y Feynman.</p>
                <p class="offer-motto">“Aprender haciendo”</p>
        </div>
    </section>

    <section class="section" id="oferta">
        <div class="wrap">
            <!-- Contenido inyectado desde data/cedic-cursos.json por cedic.js -->
            <div id="ofertaContainer">
                <div class="offer-loading">Cargando oferta formativa…</div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ SEGUIR EXPLORANDO ═══════════════════ -->
    <section class="next-nav">
        <div class="wrap">
            <div class="eyebrow">Seguir explorando</div>
            <h2 class="section-title">Otras secciones del CEDIC</h2>
            <div class="next-grid">
                <a href="contenidos.php" class="next-card">
                    <svg class="ico" aria-hidden="true"><use href="#i-ruler"/></svg>
                    <h3>Contenidos académicos</h3>
                    <p>El temario detallado de matemática y física que cubre cada curso.</p>
                    <span>Ver contenidos <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg></span>
                </a>
                <a href="actividades.php" class="next-card">
                    <svg class="ico" aria-hidden="true"><use href="#i-sparkles"/></svg>
                    <h3>Actividades</h3>
                    <p>Olimpiadas, Encomat y astronomía: la galería de nuestros encuentros.</p>
                    <span>Ver galería <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg></span>
                </a>
                <a href="cedic.php#contacto" class="next-card">
                    <svg class="ico" aria-hidden="true"><use href="#i-pin"/></svg>
                    <h3>Sede y contacto</h3>
                    <p>Dónde encontrarnos y a quién escribir para inscribirte.</p>
                    <span>Ver contacto <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg></span>
                </a>
            </div>
        </div>
    </section>

<?php include 'partials/footer.php'; ?>
