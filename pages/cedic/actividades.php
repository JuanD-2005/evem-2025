<?php
/* ══════════════════════════════════════════════════════════════════
   actividades.php — CEDIC, UNET
   El chrome vive en partials/; acá solo el contenido de la página.
   ══════════════════════════════════════════════════════════════════ */

$titulo      = 'Actividades y encuentros — CEDIC | UNET';
$descripcion = 'Galería de las olimpiadas de matemática, el Encomat y las olimpiadas de astronomía organizadas por el CEDIC en la UNET.';
$pagina      = 'actividades';

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
                    <span aria-current="page">Actividades y encuentros</span>
                </nav>
                <h1>Actividades y encuentros</h1>
                <p>Competencias, jornadas y encuentros que organizamos junto al Departamento de Matemática y Física. Las imágenes se cargan desde el servidor del portal.</p>
        </div>
    </section>

    <section class="section activities" id="actividades">
        <div class="wrap">

            <div class="activity-stack">

                <article class="activity-card reveal" id="olimpiadas">
                    <div class="activity-header">
                        <div class="activity-tag"><svg class="ico" aria-hidden="true"><use href="#i-zap"/></svg> Competencia</div>
                        <h3>Olimpiadas y Jornadas Juveniles</h3>
                        <p>
                            Las olimpiadas son un espacio para que los estudiantes demuestren sus
                            habilidades analíticas. Organizamos competencias a nivel regional y
                            nacional para descubrir a los talentos del mañana.
                        </p>
                    </div>
                    <div class="carousel-wrap" data-carousel="olim">
                        <div class="carousel-track" id="olimTrack"></div>
                        <button class="carousel-btn prev" type="button" aria-label="Imagen anterior"><svg class="ico ico-md" aria-hidden="true"><use href="#i-chevron-left"/></svg></button>
                        <button class="carousel-btn next" type="button" aria-label="Imagen siguiente"><svg class="ico ico-md" aria-hidden="true"><use href="#i-chevron-right"/></svg></button>
                        <div class="carousel-dots" id="olimDots"></div>
                    </div>
                </article>

                <article class="activity-card reveal" id="encomat">
                    <div class="activity-header">
                        <div class="activity-tag"><svg class="ico" aria-hidden="true"><use href="#i-book"/></svg> Encuentro</div>
                        <h3>Encomat</h3>
                        <p>
                            El Encuentro de Matemáticas reúne a docentes, investigadores y
                            estudiantes alrededor de la enseñanza y la divulgación de la
                            matemática, con ponencias, talleres y mesas de trabajo.
                        </p>
                    </div>
                    <div class="carousel-wrap" data-carousel="encomat">
                        <div class="carousel-track" id="encomatTrack"></div>
                        <button class="carousel-btn prev" type="button" aria-label="Imagen anterior"><svg class="ico ico-md" aria-hidden="true"><use href="#i-chevron-left"/></svg></button>
                        <button class="carousel-btn next" type="button" aria-label="Imagen siguiente"><svg class="ico ico-md" aria-hidden="true"><use href="#i-chevron-right"/></svg></button>
                        <div class="carousel-dots" id="encomatDots"></div>
                    </div>
                </article>

                <article class="activity-card reveal" id="astronomia">
                    <div class="activity-header">
                        <div class="activity-tag"><svg class="ico" aria-hidden="true"><use href="#i-sparkles"/></svg> Competencia</div>
                        <h3>Olimpiadas de Astronomía</h3>
                        <p>
                            Llevamos la mirada de los estudiantes más allá del aula: observación,
                            astrofísica básica y resolución de problemas sobre el cielo que nos
                            rodea.
                        </p>
                    </div>
                    <div class="carousel-wrap" data-carousel="astro">
                        <div class="carousel-track" id="astroTrack"></div>
                        <button class="carousel-btn prev" type="button" aria-label="Imagen anterior"><svg class="ico ico-md" aria-hidden="true"><use href="#i-chevron-left"/></svg></button>
                        <button class="carousel-btn next" type="button" aria-label="Imagen siguiente"><svg class="ico ico-md" aria-hidden="true"><use href="#i-chevron-right"/></svg></button>
                        <div class="carousel-dots" id="astroDots"></div>
                    </div>
                </article>

            </div>
        </div>
    </section>

    <!-- ═══════════════════ SEGUIR EXPLORANDO ═══════════════════ -->
    <section class="next-nav">
        <div class="wrap">
            <div class="eyebrow">Seguir explorando</div>
            <h2 class="section-title">Otras secciones del CEDIC</h2>
            <div class="next-grid">
                <a href="formacion.php" class="next-card">
                    <svg class="ico" aria-hidden="true"><use href="#i-graduation"/></svg>
                    <h3>Cursos y talleres</h3>
                    <p>Oferta formativa para estudiantes y docentes: cursos cortos, talleres y diplomados.</p>
                    <span>Ver oferta <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg></span>
                </a>
                <a href="contenidos.php" class="next-card">
                    <svg class="ico" aria-hidden="true"><use href="#i-ruler"/></svg>
                    <h3>Contenidos académicos</h3>
                    <p>El temario de matemática y física que trabajamos en cada curso.</p>
                    <span>Ver contenidos <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg></span>
                </a>
                <a href="cedic.php#equipo" class="next-card">
                    <svg class="ico" aria-hidden="true"><use href="#i-users"/></svg>
                    <h3>El equipo</h3>
                    <p>Los docentes e investigadores que dan vida al centro.</p>
                    <span>Conocer al equipo <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg></span>
                </a>
            </div>
        </div>
    </section>

<?php include 'partials/footer.php'; ?>
