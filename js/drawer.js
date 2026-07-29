/* ══════════════════════════════════════════════════════════════════
   drawer.js — navegación off-canvas para móvil

   Alterna la clase .open en el drawer y su overlay. Se cierra al tocar
   el overlay, la X, cualquier enlace, o con Escape.

   Nombres propios (.drawer-*) para no colisionar con el sistema viejo
   de js/navigation.js, que escucha .nav-toggle / .nav-mobile-menu.
   ══════════════════════════════════════════════════════════════════ */
(function () {
    const drawer = document.getElementById('siteDrawer');
    const toggle = document.querySelector('.drawer-toggle');
    const overlay = document.querySelector('.drawer-overlay');

    // Si la página no tiene drawer, el script no hace nada.
    if (!drawer || !toggle || !overlay) return;

    // El overlay nace con [hidden] para que no exista antes de que
    // el CSS esté aplicado; a partir de acá lo gobierna la clase .open.
    overlay.removeAttribute('hidden');

    function abrir() {
        drawer.classList.add('open');
        overlay.classList.add('open');
        drawer.setAttribute('aria-hidden', 'false');
        toggle.setAttribute('aria-expanded', 'true');
        toggle.setAttribute('aria-label', 'Cerrar menú de navegación');
        document.body.style.overflow = 'hidden';
    }

    function cerrar() {
        drawer.classList.remove('open');
        overlay.classList.remove('open');
        drawer.setAttribute('aria-hidden', 'true');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Abrir menú de navegación');
        document.body.style.overflow = '';
    }

    toggle.addEventListener('click', () => {
        drawer.classList.contains('open') ? cerrar() : abrir();
    });

    // Overlay y botón X comparten el atributo data-drawer-close
    document.querySelectorAll('[data-drawer-close]').forEach((el) => {
        el.addEventListener('click', cerrar);
    });

    // Al elegir un destino el menú se cierra solo
    drawer.querySelectorAll('a').forEach((a) => {
        a.addEventListener('click', cerrar);
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && drawer.classList.contains('open')) {
            cerrar();
            toggle.focus();
        }
    });

    // Si se pasa a escritorio con el menú abierto, se restablece el scroll
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768 && drawer.classList.contains('open')) {
            cerrar();
        }
    });
})();
