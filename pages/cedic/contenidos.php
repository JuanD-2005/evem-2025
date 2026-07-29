<?php
/* ══════════════════════════════════════════════════════════════════
   contenidos.php — CEDIC, UNET
   El chrome vive en partials/; acá solo el contenido de la página.
   ══════════════════════════════════════════════════════════════════ */

$titulo      = 'Contenidos académicos — CEDIC | UNET';
$descripcion = 'Programa académico del CEDIC: fundamentos matemáticos y fundamentos de física, con las competencias que desarrollan los estudiantes.';
$pagina      = 'contenidos';

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
                    <span aria-current="page">Programa académico</span>
                </nav>
                <h1>Programa académico</h1>
                <p>Los fundamentos que trabajamos en nuestros cursos y talleres, organizados por área. Estas competencias son la base para el preuniversitario y para la vida cotidiana.</p>
        </div>
    </section>

    <section class="section" id="contenidos">
        <div class="wrap">

            <div class="syllabus-grid">

                <div class="syllabus-col reveal" id="matematica">
                    <h3><svg class="ico ico-md" aria-hidden="true"><use href="#i-ruler"/></svg> Fundamentos Matemáticos</h3>
                    <p>Cinco bloques progresivos, del número real al sistema de ecuaciones.</p>

                    <details class="topic">
                        <summary>Conjuntos numéricos</summary>
                        <p>
                            Números naturales, enteros, racionales, irracionales y reales.
                            Representación en la recta real y relación de orden. Operaciones
                            aritméticas básicas en R con énfasis en racionales. Potenciación,
                            radicación y logaritmación y sus propiedades. Números primos, mínimo
                            común múltiplo y máximo común divisor. Propiedades de las operaciones
                            y su uso en la resolución de problemas con signos de agrupación y
                            simplificación de fracciones.
                        </p>
                    </details>

                    <details class="topic">
                        <summary>Polinomios</summary>
                        <p>
                            El monomio y sus elementos. Polinomios y casos especiales: monomio,
                            binomio y trinomio. Suma, resta, multiplicación y división de
                            polinomios. Productos notables como casos especiales de la
                            multiplicación. Factorización de polinomios. Fracciones algebraicas
                            como casos especiales de la división y la factorización.
                        </p>
                    </details>

                    <details class="topic">
                        <summary>Ecuaciones e inecuaciones de primer grado</summary>
                        <p>
                            Elementos de una ecuación de primer grado con una incógnita y su
                            solución. Ecuaciones de primer grado con valor absoluto. Inecuaciones
                            de primer grado con y sin valor absoluto, y representación de la
                            solución mediante intervalos de la recta real.
                        </p>
                    </details>

                    <details class="topic">
                        <summary>Ecuaciones e inecuaciones de segundo grado</summary>
                        <p>
                            Elementos de una ecuación de segundo grado con una incógnita. Raíces
                            de la ecuación. Fórmula general para su resolución. Inecuaciones de
                            segundo grado.
                        </p>
                    </details>

                    <details class="topic">
                        <summary>Sistemas de ecuaciones lineales 2x2</summary>
                        <p>
                            Resolución de sistemas de ecuaciones 2x2 mediante los métodos de
                            eliminación e igualación. Solución de un sistema en el contexto de
                            un problema.
                        </p>
                    </details>

                    <div class="competencias">
                        <h4>Competencias en aritmética y álgebra</h4>
                        <ul>
                            <li>Sumar, restar, multiplicar y dividir números enteros, fracciones y decimales.</li>
                            <li>Resolver problemas de proporcionalidad.</li>
                            <li>Resolver ecuaciones lineales y cuadráticas.</li>
                            <li>Trabajar con desigualdades.</li>
                            <li>Utilizar el álgebra para resolver problemas.</li>
                        </ul>
                    </div>
                </div>

                <div class="syllabus-col reveal" id="fisica">
                    <h3><svg class="ico ico-md" aria-hidden="true"><use href="#i-atom"/></svg> Fundamentos de Física</h3>
                    <p>Del método científico a los principios energéticos.</p>

                    <details class="topic">
                        <summary>Método científico</summary>
                        <p>
                            Proceso sistemático de investigación que se utiliza para generar
                            conocimiento: observar y describir fenómenos naturales o artificiales,
                            formular hipótesis que los expliquen, diseñar y realizar experimentos
                            para probarlas, analizar datos y llegar a conclusiones, y comunicar
                            los resultados de la investigación.
                        </p>
                    </details>

                    <details class="topic">
                        <summary>La experimentación en la Física</summary>
                        <p>
                            La experimentación es una parte fundamental de la física: los
                            experimentos se utilizan para probar las leyes y teorías físicas.
                            Los estudiantes aprenden a realizar experimentos de manera segura
                            y eficiente.
                        </p>
                    </details>

                    <details class="topic">
                        <summary>Cinemática</summary>
                        <p>
                            Rama de la física que se ocupa del movimiento de los objetos. Se
                            divide en movimiento rectilíneo uniforme (velocidad constante en
                            línea recta) y movimiento rectilíneo uniformemente acelerado.
                            Competencias: definir las variables que describen el movimiento,
                            utilizar las ecuaciones de la cinemática para calcularlo y aplicar
                            sus leyes a situaciones reales.
                        </p>
                    </details>

                    <details class="topic">
                        <summary>Dinámica</summary>
                        <p>
                            Rama que se ocupa de las causas del movimiento, basada en las leyes
                            de Newton: la fuerza es la causa del cambio de movimiento; la fuerza
                            es igual a la masa por la aceleración; acción y reacción son iguales
                            y opuestas. Competencias: definir las variables que describen la
                            fuerza, utilizar las leyes de Newton para calcular la fuerza sobre un
                            objeto y aplicarlas a situaciones reales.
                        </p>
                    </details>

                    <details class="topic">
                        <summary>Trabajo y energía</summary>
                        <p>
                            Muchas situaciones se resuelven de forma más sencilla empleando
                            principios energéticos. Se estudia la definición de trabajo, la
                            energía cinética y potencial, los teoremas del trabajo y la energía,
                            y el principio de conservación de la energía. Competencias: definir
                            las fuerzas conservativas y utilizar los teoremas del trabajo y la
                            energía para resolver ejercicios.
                        </p>
                    </details>

                    <div class="competencias">
                        <h4>Por qué importa</h4>
                        <ul>
                            <li>Estas habilidades son fundamentales para el éxito en el preuniversitario.</li>
                            <li>Son la base para el cálculo, la matemática aplicada y la ingeniería.</li>
                            <li>Quienes las dominan enfrentan mejor preparados los desafíos del mundo real.</li>
                        </ul>
                    </div>
                </div>

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
                    <p>Dónde se dictan estos contenidos: la oferta formativa completa.</p>
                    <span>Ver oferta <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg></span>
                </a>
                <a href="actividades.php" class="next-card">
                    <svg class="ico" aria-hidden="true"><use href="#i-sparkles"/></svg>
                    <h3>Actividades</h3>
                    <p>Olimpiadas, Encomat y astronomía: la galería de nuestros encuentros.</p>
                    <span>Ver galería <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg></span>
                </a>
                <a href="cedic.php#nosotros" class="next-card">
                    <svg class="ico" aria-hidden="true"><use href="#i-flask"/></svg>
                    <h3>El Centro</h3>
                    <p>Qué es el CEDIC y por qué la divulgación científica importa.</p>
                    <span>Conocer el centro <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg></span>
                </a>
            </div>
        </div>
    </section>

<?php include 'partials/footer.php'; ?>
