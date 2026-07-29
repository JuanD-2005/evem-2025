<?php
/* ══════════════════════════════════════════════════════════════════
   partials/nav.php — navbar + panel móvil

   Solo markup: la navegación se declara en config.php.
   Para agregar o mover un enlace se edita ese archivo, no este.
   ══════════════════════════════════════════════════════════════════ */

$href_inicio = ($pagina === 'inicio') ? '#top' : $LANDING;
$href_cta    = cedic_ancla('contacto', $pagina, $LANDING);
?>
    <!-- ═══════════════════ NAVBAR ═══════════════════ -->
    <header class="top-bar" id="topBar">
        <div class="nav-inner">

            <div class="nav-left">
                <a href="../../index.html" class="nav-back">
                    <svg class="ico" aria-hidden="true"><use href="#i-chevron-left"/></svg>
                    <span class="nav-back-label">EVEM</span>
                </a>

                <nav aria-label="Secciones del portal CEDIC">
                    <ul class="nav-menu">
<?php foreach ($NAV_BARRA as $item): ?>
                        <li><a href="<?= cedic_href($item, $pagina, $LANDING) ?>"<?= cedic_activo($item, $pagina) ?>><?= $item['label'] ?></a></li>
<?php endforeach; ?>
                    </ul>
                </nav>
            </div>

            <a href="<?= $href_inicio ?>" class="central-logo-container" aria-label="CEDIC — inicio">
                <img src="../../assets/logos/cedic-Sinfondo.png" alt="Logo CEDIC" class="central-logo">
            </a>

            <div class="nav-right">
                <a href="<?= $href_cta ?>" class="nav-cta">Contáctanos</a>
                <button class="nav-toggle" type="button" id="navToggle"
                        aria-expanded="false" aria-controls="navPanel" aria-label="Abrir menú de navegación">
                    <svg class="ico ico-open" aria-hidden="true"><use href="#i-menu"/></svg>
                    <svg class="ico ico-close" aria-hidden="true"><use href="#i-close"/></svg>
                </button>
            </div>
        </div>

        <!-- Menú desplegable para móvil -->
        <div class="nav-panel" id="navPanel">
            <ul>
<?php foreach ($NAV_PANEL as $item): ?>
                <li><a href="<?= cedic_href($item, $pagina, $LANDING) ?>"<?= cedic_activo($item, $pagina) ?>><?= $item['label'] ?> <svg class="ico" aria-hidden="true"><use href="#i-chevron-right"/></svg></a></li>
<?php endforeach; ?>
            </ul>
        </div>
    </header>
