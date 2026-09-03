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
                <button class="drawer-toggle" type="button"
                        aria-expanded="false" aria-controls="siteDrawer" aria-label="Abrir menú de navegación">
                    <svg class="ico" aria-hidden="true"><use href="#i-menu"/></svg>
                </button>

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
            </div>
        </div>
    </header>

    <!-- ═══════════════════ DRAWER LATERAL (off-canvas, solo móvil) ═══════════════════ -->
    <div class="drawer-overlay" data-drawer-close hidden></div>

    <aside class="drawer-menu drawer-dark" id="siteDrawer" aria-label="Menú de navegación" aria-hidden="true">
        <div class="drawer-head">
            <span class="drawer-logo-chip">
                <img src="../../assets/logos/cedic-Sinfondo.png" alt="">
            </span>
            <span class="drawer-head-text">
                <strong>CEDIC</strong>
                <small>Centro de Ciencias · UNET</small>
            </span>
            <button class="drawer-close" type="button" aria-label="Cerrar menú" data-drawer-close>
                <svg class="ico" aria-hidden="true"><use href="#i-close"/></svg>
            </button>
        </div>

        <nav class="drawer-nav" aria-label="Secciones del portal CEDIC">
<?php foreach ($NAV_PANEL as $item):
    $activo = ($item['clave'] ?? null) === $pagina;
?>
            <a href="<?= cedic_href($item, $pagina, $LANDING) ?>" class="drawer-link<?= $activo ? ' active' : '' ?>">
                <svg class="ico" aria-hidden="true"><use href="#<?= $item['icono'] ?? 'i-arrow-right' ?>"/></svg>
                <?= $item['label'] ?>
            </a>
<?php endforeach; ?>
        </nav>

        <div class="drawer-foot">
            <a href="<?= $href_cta ?>" class="drawer-cta">
                <svg class="ico" aria-hidden="true"><use href="#i-mail"/></svg>
                Contáctanos
            </a>
        </div>
    </aside>
