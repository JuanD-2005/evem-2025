<?php
/* ══════════════════════════════════════════════════════════════════
   partials/config.php — configuración compartida del portal CEDIC

   Único lugar donde se declaran la navegación y los destinos.
   Lo incluye head.php, así que está disponible en nav.php,
   footer.php y en el cuerpo de cada página.
   ══════════════════════════════════════════════════════════════════ */

/* ─────────────────────────────────────────────────────────────────
   INTERRUPTOR DEL SPLIT

   false → Actividades, Formación y Contenidos son secciones dentro
           de cedic.php y el navbar apunta a sus anclas.
   true  → existen actividades.php, formacion.php y contenidos.php
           y el navbar apunta a esos archivos.

   Poner en true recién cuando los tres archivos estén subidos:
   antes de eso el menú devolvería 404.
   ───────────────────────────────────────────────────────────────── */
$PAGINAS_SEPARADAS = true;

$LANDING = 'cedic.php';

/* Destinos que cambian de forma según el interruptor. */
$DESTINOS = [
    'actividades' => $PAGINAS_SEPARADAS
        ? ['tipo' => 'pagina', 'destino' => 'actividades.php', 'clave' => 'actividades']
        : ['tipo' => 'ancla',  'destino' => 'actividades'],
    'formacion'   => $PAGINAS_SEPARADAS
        ? ['tipo' => 'pagina', 'destino' => 'formacion.php',   'clave' => 'formacion']
        : ['tipo' => 'ancla',  'destino' => 'oferta'],
    'contenidos'  => $PAGINAS_SEPARADAS
        ? ['tipo' => 'pagina', 'destino' => 'contenidos.php',  'clave' => 'contenidos']
        : ['tipo' => 'ancla',  'destino' => 'contenidos'],
];

/* Barra horizontal (escritorio). Máximo 4 entradas: más no caben sin
   chocar con la gota central; ver el breakpoint de 1024px en cedic.css. */
$NAV_BARRA = [
    ['label' => 'El Centro', 'tipo' => 'ancla', 'destino' => 'nosotros'],
    ['label' => 'Actividades'] + $DESTINOS['actividades'],
    ['label' => 'Formación']   + $DESTINOS['formacion'],
    ['label' => 'Equipo',    'tipo' => 'ancla', 'destino' => 'equipo'],
];

/* Panel desplegable (móvil): navegación completa. */
$NAV_PANEL = [
    ['label' => 'El Centro',       'tipo' => 'ancla', 'destino' => 'nosotros'],
    ['label' => 'Iniciativas',     'tipo' => 'ancla', 'destino' => 'iniciativas'],
    ['label' => 'Actividades'] + $DESTINOS['actividades'],
    ['label' => 'Formación']   + $DESTINOS['formacion'],
    ['label' => 'Contenidos']  + $DESTINOS['contenidos'],
    ['label' => 'Equipo',          'tipo' => 'ancla', 'destino' => 'equipo'],
    ['label' => 'Sede y contacto', 'tipo' => 'ancla', 'destino' => 'contacto'],
];

/**
 * Resuelve el href de una entrada de navegación.
 * Un ancla vista desde una subpágina tiene que volver a la landing.
 */
function cedic_href(array $item, string $pagina, string $landing): string {
    if (($item['tipo'] ?? 'ancla') === 'pagina') {
        return $item['destino'];
    }
    $prefijo = ($pagina === 'inicio') ? '' : $landing;
    return $prefijo . '#' . $item['destino'];
}

/**
 * Marca .active en enlaces de página. Las anclas las resalta el
 * scrollspy de cedic.js, que ignora los href que no empiezan con '#'.
 */
function cedic_activo(array $item, string $pagina): string {
    return (($item['clave'] ?? null) === $pagina) ? ' class="active"' : '';
}

/** Ancla suelta de la landing, resuelta según la página actual. */
function cedic_ancla(string $id, string $pagina, string $landing): string {
    return (($pagina === 'inicio') ? '' : $landing) . '#' . $id;
}
