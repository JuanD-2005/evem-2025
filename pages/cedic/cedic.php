<?php
/* ══════════════════════════════════════════════════════════════════
   cedic.php — landing del Centro de Enseñanza y Divulgación
              de las Ciencias (CEDIC), UNET.

   Actividades, Formación y Contenidos aparecen acá como resúmenes
   con enlace a su página propia: actividades.php, formacion.php y
   contenidos.php. El chrome vive en partials/.
   ══════════════════════════════════════════════════════════════════ */

$titulo      = 'CEDIC — Centro de Enseñanza y Divulgación de las Ciencias | UNET';
$descripcion = 'El CEDIC es un centro dedicado a la formación y actualización de profesionales en la didáctica de la matemática, la física y la química. Departamento de Matemática y Física, UNET.';
$pagina      = 'inicio';

include 'partials/head.php';
?>
    <!-- ═══════════════════ HERO ═══════════════════ -->
    <section class="hero" id="top">
        <div class="hero-grid-bg"></div>
        <div class="hero-glow"></div>
        <div class="wrap hero-inner">
            <div>
                <div class="hero-badge">
                    <span class="pulse"></span>
                    Universidad Nacional Experimental del Táchira
                </div>
                <h1 class="hero-title">
                    Centro de Enseñanza y<br>
                    <span>Divulgación de las Ciencias</span>
                </h1>
                <p class="hero-motto">“Aprender haciendo”</p>
                <p class="hero-sub">
                    Un centro innovador dedicado a la formación y actualización de profesionales
                    en la didáctica de la matemática, la física y la química, y a despertar la
                    pasión por la ciencia en estudiantes y público en general.
                </p>
                <div class="hero-actions">
                    <a href="#iniciativas" class="btn btn-primary">
                        Explorar iniciativas
                        <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg>
                    </a>
                    <a href="#equipo" class="btn btn-ghost">Conocer al equipo</a>
                </div>
            </div>

            <div class="hero-visual">
                <img src="../../assets/logos/cedic-Sinfondo.png"
                     alt="Logo del Centro de Enseñanza y Divulgación de las Ciencias"
                     class="hero-logo">
            </div>
        </div>
    </section>

    <!-- ═══════════════════ SOBRE EL CEDIC ═══════════════════ -->
    <section class="about" id="nosotros">
        <div class="wrap about-grid">

            <div class="glass-card reveal">
                <div class="eyebrow">Quiénes somos</div>
                <h2 class="section-title">¿Por qué la ciencia es importante?</h2>

                <p class="pull-quote">
                    Las ciencias fundamentales son la base del desarrollo tecnológico y social.
                    Sin ellas no sería posible el progreso en áreas como la medicina, la
                    ingeniería, la agricultura o la comunicación.
                </p>

                <div class="about-body">
                    <p>
                        Sin embargo, muchos estudiantes las consideran aburridas o difíciles.
                        Nuestro objetivo principal es despertar la pasión por la ciencia en
                        estudiantes y público en general, combatiendo la apatía y los
                        estereotipos que la rodean.
                    </p>

                    <h3 class="about-sub">¿Qué hacemos?</h3>
                    <p>
                        El CEDIC ofrece una amplia gama de programas y actividades para fomentar
                        el aprendizaje de las ciencias de una manera divertida, interactiva y
                        atractiva. Es una iniciativa del Departamento de Matemática y Física y
                        del Proyecto AULA, concebida por los profesores Gilberto Paredes,
                        Blanca Gámez, Blanca Guillén, Tania Peña, Emildo Marcano y Mayrin Cárdenas.
                    </p>
                </div>

                <!-- Cifras derivadas de la oferta real documentada, no estimaciones -->
                <div class="stats">
                    <div class="stat">
                        <div class="stat-num">3</div>
                        <div class="stat-label">Ciencias fundamentales</div>
                    </div>
                    <div class="stat">
                        <div class="stat-num">12</div>
                        <div class="stat-label">Cursos cortos</div>
                    </div>
                    <div class="stat">
                        <div class="stat-num">3</div>
                        <div class="stat-label">Diplomados</div>
                    </div>
                    <div class="stat">
                        <div class="stat-num">6</div>
                        <div class="stat-label">Docentes fundadores</div>
                    </div>
                </div>
            </div>

            <div class="about-side">
                <article class="pillar pillar-teach reveal">
                    <div>
                        <span class="pillar-icon"><svg class="ico ico-lg" aria-hidden="true"><use href="#i-graduation"/></svg></span>
                        <h3>Formación docente</h3>
                        <p>
                            Cursos y talleres para actualizar a los profesores en las últimas
                            tendencias de la enseñanza de las ciencias, con metodologías
                            innovadoras que hacen las clases más dinámicas y participativas.
                        </p>
                    </div>
                    <div class="pillar-blob"></div>
                </article>

                <article class="pillar pillar-share reveal">
                    <div>
                        <span class="pillar-icon"><svg class="ico ico-lg" aria-hidden="true"><use href="#i-flask"/></svg></span>
                        <h3>Divulgación científica</h3>
                        <p>
                            Charlas, conferencias, exposiciones, demostraciones y festivales
                            para acercar la ciencia al público general y despertar la
                            curiosidad científica.
                        </p>
                    </div>
                    <div class="pillar-blob"></div>
                </article>
            </div>

        </div>
    </section>

    <!-- ═══════════════════ ECOSISTEMA (BENTO) ═══════════════════ -->
    <section class="section" id="iniciativas">
        <div class="wrap">
            <div class="bento-head reveal">
                <div>
                    <div class="eyebrow">Ecosistema</div>
                    <h2 class="section-title">Eventos e iniciativas</h2>
                    <p class="section-lead">
                        El CEDIC participa en la organización de los encuentros científicos de la
                        UNET. Cada uno tiene su propio espacio dentro del portal.
                    </p>
                </div>
            </div>

            <div class="bento">

                <a href="../../index.html" class="bento-item bento-evem reveal">
                    <div>
                        <span class="bento-tag">Evento principal</span>
                        <h3>EVEM 2026</h3>
                        <p>
                            El Encuentro Venezolano de Educación Matemática reúne a docentes,
                            investigadores y estudiantes de todo el país. La cita mayor del
                            calendario científico de la UNET, sede San Cristóbal.
                        </p>
                    </div>
                    <span class="bento-link">
                        Ir al portal EVEM
                        <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg>
                    </span>
                    <span class="bento-watermark">E</span>
                </a>

                <a href="../festival/festival.html" class="bento-item bento-festival reveal">
                    <span class="bento-emoji"><svg class="ico" aria-hidden="true"><use href="#i-rocket"/></svg></span>
                    <div>
                        <span class="bento-tag">Evento anual</span>
                        <h3>Festival de las Ciencias</h3>
                        <p>
                            Conferencias con ponentes certificados, áreas temáticas y programa
                            académico abierto a toda la comunidad.
                        </p>
                        <span class="bento-link">
                            Ver programa
                            <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg>
                        </span>
                    </div>
                </a>

                <a href="../dim/dim.html" class="bento-item bento-dim reveal">
                    <span class="bento-tag">Jornada</span>
                    <h3>DIM</h3>
                    <p>Día Internacional de las Matemáticas: actividades breves y de alto impacto.</p>
                    <span class="bento-link">
                        Conocer más
                        <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg>
                    </span>
                </a>

                <!-- Encomat no tiene página propia: ancla a la galería de actividades (v2) -->
                <a href="#actividades" class="bento-item bento-encomat reveal">
                    <span class="bento-tag">Encuentro</span>
                    <h3>Encomat</h3>
                    <p>Encuentro de matemáticas con olimpiadas, jornadas juveniles y astronomía.</p>
                    <span class="bento-soon">Galería próximamente</span>
                </a>

            </div>
        </div>
    </section>

    <!-- ═══════════════════ ACTIVIDADES ═══════════════════ -->

    <!-- ═══════════ ACTIVIDADES (resumen → actividades.php) ═══════════ -->
    <section class="section activities" id="actividades">
        <div class="wrap">
            <div class="section-head reveal">
                <div>
                    <div class="eyebrow">Galería</div>
                    <h2 class="section-title">Actividades y encuentros</h2>
                    <p class="section-lead">
                        Competencias, jornadas y encuentros que organizamos junto al
                        Departamento de Matemática y Física.
                    </p>
                </div>
                <a href="actividades.php" class="section-cta">
                    Ver todas las galerías
                    <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg>
                </a>
            </div>

            <!-- La portada de cada tarjeta la pone cedic.js con la primera
                 imagen de la carpeta indicada en data-cover -->
            <div class="teaser-grid reveal">
                <a href="actividades.php#olimpiadas" class="teaser-card">
                    <span class="teaser-cover" data-cover="carruseluno"></span>
                    <div class="teaser-body">
                        <div class="activity-tag">
                            <svg class="ico" aria-hidden="true"><use href="#i-zap"/></svg>
                            Competencia
                        </div>
                        <h3>Olimpiadas y Jornadas Juveniles</h3>
                        <p>Competencias regionales y nacionales para descubrir a los talentos del mañana.</p>
                        <span class="ver">Ver galería <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg></span>
                    </div>
                </a>

                <a href="actividades.php#encomat" class="teaser-card">
                    <span class="teaser-cover" data-cover="carruseldos"></span>
                    <div class="teaser-body">
                        <div class="activity-tag">
                            <svg class="ico" aria-hidden="true"><use href="#i-book"/></svg>
                            Encuentro
                        </div>
                        <h3>Encomat</h3>
                        <p>Ponencias, talleres y mesas de trabajo alrededor de la enseñanza de la matemática.</p>
                        <span class="ver">Ver galería <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg></span>
                    </div>
                </a>

                <a href="actividades.php#astronomia" class="teaser-card">
                    <span class="teaser-cover" data-cover="carruseltres"></span>
                    <div class="teaser-body">
                        <div class="activity-tag">
                            <svg class="ico" aria-hidden="true"><use href="#i-sparkles"/></svg>
                            Competencia
                        </div>
                        <h3>Olimpiadas de Astronomía</h3>
                        <p>Observación, astrofísica básica y resolución de problemas sobre el cielo.</p>
                        <span class="ver">Ver galería <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg></span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- ═══════════ FORMACIÓN (resumen → formacion.php) ═══════════ -->
    <section class="section" id="oferta">
        <div class="wrap">
            <div class="section-head reveal">
                <div>
                    <div class="eyebrow">Formación</div>
                    <h2 class="section-title">Cursos, talleres y diplomados</h2>
                    <p class="section-lead">
                        Inmersión práctica en las ciencias para estudiantes y docentes,
                        con las metodologías STEAM y Feynman.
                    </p>
                    <p class="offer-motto">“Aprender haciendo”</p>
                </div>
                <a href="formacion.php" class="section-cta">
                    Ver la oferta completa
                    <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg>
                </a>
            </div>

            <!-- Tarjetas generadas desde data/cedic-cursos.json por cedic.js -->
            <div class="teaser-programs reveal" id="programasTeaser">
                <div class="offer-loading">Cargando programas…</div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ EQUIPO ═══════════════════ -->
    <section class="section team" id="equipo">
        <div class="wrap">
            <div class="reveal">
                <div class="eyebrow">Staff</div>
                <h2 class="section-title">Nuestros profesores</h2>
                <p class="section-lead">
                    Docentes e investigadores del Departamento de Matemática y Física de la UNET
                    que dan vida al centro.
                </p>
            </div>

            <div class="team-grid">

                <article class="member reveal">
                    <div class="member-top">
                        <img src="../../assets/cedic/staff/gilberto-paredes.jpg" alt="Dr. Gilberto Paredes"
                             class="member-photo"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="member-initials">GP</div>
                        <div>
                            <h3>Dr. Gilberto Paredes</h3>
                            <p class="member-role">Responsable del Laboratorio de Física Aplicada y Computacional (LFAC)</p>
                        </div>
                    </div>
                    <p class="member-bio clamped">
                        Licenciado en Física Pura. Realizó estudios doctorales en el Centro de
                        Física Fundamental de la ULA, especializándose en caos, dinámica no lineal
                        y sistemas complejos. Ha escrito varios artículos científicos en revistas
                        internacionales relacionados con sus campos de conocimiento. Actualmente
                        es responsable del Programa de Maestría en Matemáticas – Educación
                        Matemática de la UNET.
                    </p>
                    <button class="bio-toggle" type="button">Leer más</button>
                </article>

                <article class="member reveal">
                    <div class="member-top">
                        <img src="../../assets/cedic/staff/tania-pena.jpg" alt="Dra. Tania Peña"
                             class="member-photo"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="member-initials">TP</div>
                        <div>
                            <h3>Dra. Tania Peña</h3>
                            <p class="member-role">Profesora Titular a Dedicación Exclusiva</p>
                        </div>
                    </div>
                    <p class="member-bio clamped">
                        Doctora en Innovación Educativa, Magíster en Educación Matemática y
                        Licenciada en Educación mención Matemática e Informática. Profesora
                        Titular a Dedicación Exclusiva adscrita al Departamento de Matemática y
                        Física de la UNET. Integra el Laboratorio de Investigación de Matemática
                        Pura y Aplicada (LIMPA). Sus áreas de interés investigativo son la
                        educación matemática, la innovación educativa y los ambientes de
                        aprendizaje personalizados.
                    </p>
                    <button class="bio-toggle" type="button">Leer más</button>
                </article>

                <article class="member reveal">
                    <div class="member-top">
                        <img src="../../assets/cedic/staff/blanca-guillen.jpg" alt="Dra. Blanca Guillén"
                             class="member-photo"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="member-initials">BG</div>
                        <div>
                            <h3>Dra. Blanca Guillén</h3>
                            <p class="member-role">Investigadora — Grupo de Bioingeniería</p>
                        </div>
                    </div>
                    <p class="member-bio clamped">
                        Licenciada en Matemáticas con distinción Cum Laude (1996) y Magíster
                        Scientiae en Matemáticas (1999) por la Universidad de los Andes,
                        Mérida. Doctora en Ingeniería (2013) por la Universidad Simón Bolívar,
                        Caracas. Es profesora del Departamento de Matemáticas de la UNET e
                        investigadora del Grupo de Bioingeniería de esta misma casa de estudios.
                        Dentro del ámbito de las matemáticas su área de interés es el análisis
                        numérico.
                    </p>
                    <button class="bio-toggle" type="button">Leer más</button>
                </article>

                <article class="member reveal">
                    <div class="member-top">
                        <img src="../../assets/cedic/staff/blanca-gamez.jpg" alt="MSc. Blanca Gámez"
                             class="member-photo"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="member-initials">BG</div>
                        <div>
                            <h3>MSc. Blanca Gámez</h3>
                            <p class="member-role">Profesora Asociada — Cátedra de Matemática</p>
                        </div>
                    </div>
                    <p class="member-bio clamped">
                        Profesora Asociada de la UNET y Magíster en Matemática, adscrita al
                        Departamento de Matemática y Física en la Cátedra de Matemática. Imparte
                        Matemática I, II, III y IV, y Lógica Matemática en el Decanato de
                        Postgrado. Es profesora en formación permanente de los cursos de
                        Matemática Preuniversitaria, Funciones y Graficación, y Matemática de
                        5.º y 6.º grado.
                    </p>
                    <button class="bio-toggle" type="button">Leer más</button>
                </article>

            </div>
        </div>
    </section>

    <!-- ═══════════════════ CONTENIDOS PROGRAMÁTICOS ═══════════════════ -->

    <!-- ═══════════ CONTENIDOS (resumen → contenidos.php) ═══════════ -->
    <section class="section" id="contenidos">
        <div class="wrap">
            <div class="section-head reveal">
                <div>
                    <div class="eyebrow">Contenidos</div>
                    <h2 class="section-title">Programa académico</h2>
                    <p class="section-lead">
                        El temario que trabajamos en cada curso, organizado por área.
                    </p>
                </div>
                <a href="contenidos.php" class="section-cta">
                    Ver el temario completo
                    <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg>
                </a>
            </div>

            <div class="teaser-contents reveal">
                <a href="contenidos.php#matematica" class="content-card">
                    <span class="badge"><svg class="ico" aria-hidden="true"><use href="#i-ruler"/></svg></span>
                    <div>
                        <h3>Fundamentos Matemáticos</h3>
                        <p>Cinco bloques progresivos, del número real al sistema de ecuaciones.</p>
                    </div>
                    <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg>
                </a>

                <a href="contenidos.php#fisica" class="content-card">
                    <span class="badge"><svg class="ico" aria-hidden="true"><use href="#i-atom"/></svg></span>
                    <div>
                        <h3>Fundamentos de Física</h3>
                        <p>Del método científico a los principios energéticos.</p>
                    </div>
                    <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ SEDE Y CONTACTO ═══════════════════ -->
    <section class="section venue" id="contacto">
        <div class="wrap venue-grid">

            <div class="reveal">
                <div class="eyebrow" style="color:#7dd3fc;">Sede</div>
                <h2 class="section-title">Dónde encontrarnos</h2>
                <p class="venue-address">
                    CEDIC — Edificio F, antigua Casita de AsoVAC.<br>
                    Universidad Nacional Experimental del Táchira, San Cristóbal.
                </p>
                <span class="venue-ref"><svg class="ico" aria-hidden="true"><use href="#i-pin"/></svg> Punto de referencia: AULA VIVA</span>
            </div>

            <div class="contact-card reveal">
                <h3>Información e inscripciones</h3>

                <div class="contact-row">
                    <svg class="ico ico-md contact-ico" aria-hidden="true"><use href="#i-phone"/></svg>
                    <div>
                        <p class="contact-label">Formación Permanente — UNET</p>
                        <p class="contact-value"><a href="tel:+584247235172">0424-7235172</a></p>
                    </div>
                </div>

                <div class="contact-row">
                    <svg class="ico ico-md contact-ico" aria-hidden="true"><use href="#i-smartphone"/></svg>
                    <div>
                        <p class="contact-label">CEDIC — UNET</p>
                        <p class="contact-value contact-value-multi">
                            <a href="tel:+584247077573">0424-7077573</a>
                            <a href="tel:+584166314095">0416-6314095</a>
                        </p>
                    </div>
                </div>

                <div class="contact-row">
                    <svg class="ico ico-md contact-ico" aria-hidden="true"><use href="#i-mail"/></svg>
                    <div>
                        <p class="contact-label">Correo</p>
                        <p class="contact-value"><a href="mailto:evem@unet.edu.ve">evem@unet.edu.ve</a></p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ═══════════════════ CTA FINAL ═══════════════════ -->
    <section class="final-cta">
        <div class="wrap">
            <div class="final-cta-box reveal">
                <h2>¿Te apasiona la ciencia?</h2>
                <p>
                    El CEDIC te invita a formar parte de esta comunidad y a contribuir a la
                    construcción de una sociedad más científica y tecnológicamente avanzada.
                    Ven a descubrir el mundo apasionante de la ciencia.
                </p>
                <div class="hero-actions">
                    <a href="#contacto" class="btn btn-dark">
                        Escríbenos
                        <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg>
                    </a>
                    <a href="../festival/festival.html" class="btn btn-outline">Ver el Festival de las Ciencias</a>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ FOOTER ═══════════════════ -->

<?php include 'partials/footer.php'; ?>
