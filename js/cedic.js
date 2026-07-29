/* ══════════════════════════════════════════════════════════════════
   CEDIC — script compartido del portal
   Lo cargan cedic.php, actividades.php, formacion.php y contenidos.php.

   Cada módulo comprueba que sus elementos existan antes de correr,
   porque no todas las páginas tienen carrusel, oferta o fichas de
   equipo. Agregar módulos siguiendo esa misma regla.
   ══════════════════════════════════════════════════════════════════ */

// ── Escapado de texto para todo el HTML que se genera desde JS ──
// Va al tope a propósito: los módulos de abajo lo usan y `const` no se
// iza (queda en zona muerta temporal). Declararlo al final funcionaba
// solo porque el `await fetch` cedía el control y dejaba terminar de
// evaluar el script; cualquier reordenamiento lo rompería.
const esc = s => String(s).replace(/[&<>"']/g,
    m => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m]));

// ── Navbar: contrae la gota al hacer scroll ──
// En reposo mide 104px (más que los 64px del navbar) por diseño de marca;
// al scrollear se encoge al círculo compacto que ya usa el breakpoint móvil.
const topBar = document.getElementById('topBar');

if (topBar) {
    let logoContraido = false;
    window.addEventListener('scroll', () => {
        const debeContraerse = window.scrollY > 40;
        if (debeContraerse !== logoContraido) {
            logoContraido = debeContraerse;
            topBar.classList.toggle('scrolled', debeContraerse);
        }
    }, { passive: true });
}

// ── Animaciones de revelado ──
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.classList.add('visible');
            observer.unobserve(e.target);
        }
    });
}, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// ══════════════════ NAVEGACIÓN ══════════════════
// Panel móvil
const navToggle = document.getElementById('navToggle');
const navPanel  = document.getElementById('navPanel');

if (navToggle && navPanel) {
    navToggle.addEventListener('click', () => {
        const abierto = navPanel.classList.toggle('open');
        navToggle.setAttribute('aria-expanded', String(abierto));
        navToggle.setAttribute('aria-label', abierto ? 'Cerrar menú de navegación' : 'Abrir menú de navegación');
    });

    // Al elegir un destino, el panel se cierra solo
    navPanel.querySelectorAll('a').forEach(a => {
        a.addEventListener('click', () => {
            navPanel.classList.remove('open');
            navToggle.setAttribute('aria-expanded', 'false');
        });
    });

    // Escape cierra el panel
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && navPanel.classList.contains('open')) {
            navPanel.classList.remove('open');
            navToggle.setAttribute('aria-expanded', 'false');
            navToggle.focus();
        }
    });
}

// Scrollspy: resalta el enlace de la sección que se está viendo.
// Se observa una franja delgada bajo el header para evitar que dos
// secciones queden "activas" a la vez en pantallas altas.
const enlacesNav = [...document.querySelectorAll('.nav-menu a')];
const seccionesNav = enlacesNav
    .map(a => a.getAttribute('href'))
    .filter(h => h && h.startsWith('#'))        // descarta enlaces a otras páginas
    .map(h => document.querySelector(h))
    .filter(Boolean);

if (seccionesNav.length) {
    const spy = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const id = '#' + entry.target.id;
            enlacesNav
                .filter(a => (a.getAttribute('href') || '').startsWith('#'))
                .forEach(a => a.classList.toggle('active', a.getAttribute('href') === id));
        });
    }, { rootMargin: '-80px 0px -70% 0px', threshold: 0 });

    seccionesNav.forEach(s => spy.observe(s));
}

// ══════════════════ CARRUSELES ══════════════════
// Migrado de mat.html. Los nombres de carpeta son los del backend actual;
// si se renombran en el servidor, solo hay que cambiarlos acá.
const CARRUSELES = [
    { id: 'olim',    carpeta: 'carruseluno',  intervalo: 5000 },
    { id: 'encomat', carpeta: 'carruseldos',  intervalo: 6500 },
    { id: 'astro',   carpeta: 'carruseltres', intervalo: 6000 }
];

const estado = {};

async function cargarCarrusel({ id, carpeta, intervalo }) {
    const track = document.getElementById(id + 'Track');
    const dots  = document.getElementById(id + 'Dots');
    const wrap  = document.querySelector(`[data-carousel="${id}"]`);
    if (!track || !wrap) return;

    estado[id] = { index: 0, total: 0, track, dots, timer: null };

    try {
        const res = await fetch(`../../backend/api.php?action=get_carousel_images&folder=${carpeta}`);
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const imagenes = await res.json();

        if (!Array.isArray(imagenes) || imagenes.length === 0) {
            track.innerHTML = `<div class="carousel-empty">
                <svg class="ico" style="width:2rem;height:2rem;opacity:.5" aria-hidden="true"><use href="#i-image"/></svg>
                <span>Aún no hay imágenes en esta galería</span>
            </div>`;
            return;
        }

        // Las rutas las arma el backend con scandir() sobre la carpeta, así que
        // el nombre de archivo del servidor entra al HTML: se escapa igual que
        // cualquier otro dato externo.
        track.innerHTML = imagenes
            .map(src => `<img src="${esc(src)}" alt="Actividad del CEDIC" loading="lazy">`)
            .join('');

        estado[id].total = imagenes.length;

        if (imagenes.length > 1) {
            dots.innerHTML = imagenes
                .map((_, i) => `<button class="dot${i === 0 ? ' active' : ''}" type="button"
                      data-slide="${i}" aria-label="Ir a la imagen ${i + 1}"></button>`)
                .join('');
            iniciarAutoplay(id, intervalo);
        }
    } catch (error) {
        console.error(`[carrusel:${carpeta}] no se pudieron cargar las imágenes:`, error);
        track.innerHTML = `<div class="carousel-empty">
            <svg class="ico" style="width:2rem;height:2rem;opacity:.5" aria-hidden="true"><use href="#i-alert"/></svg>
            <span>La galería no está disponible en este momento</span>
        </div>`;
    }
}

function actualizar(id) {
    const c = estado[id];
    if (!c || c.total === 0) return;
    c.track.style.transform = `translateX(-${c.index * 100}%)`;
    c.dots.querySelectorAll('.dot')
          .forEach((d, i) => d.classList.toggle('active', i === c.index));
}

function mover(id, dir) {
    const c = estado[id];
    if (!c || c.total === 0) return;
    c.index = (c.index + dir + c.total) % c.total;
    actualizar(id);
}

function iniciarAutoplay(id, intervalo) {
    const c = estado[id];
    const wrap = document.querySelector(`[data-carousel="${id}"]`);
    c.timer = setInterval(() => mover(id, 1), intervalo);

    // Pausa mientras el usuario está mirando o interactuando
    const pausar  = () => clearInterval(c.timer);
    const reanudar = () => { clearInterval(c.timer); c.timer = setInterval(() => mover(id, 1), intervalo); };
    wrap.addEventListener('mouseenter', pausar);
    wrap.addEventListener('mouseleave', reanudar);
    wrap.addEventListener('focusin', pausar);
    wrap.addEventListener('focusout', reanudar);
}

// Un solo listener para flechas y dots de todos los carruseles
document.querySelectorAll('[data-carousel]').forEach(wrap => {
    const id = wrap.dataset.carousel;
    wrap.addEventListener('click', e => {
        const dot = e.target.closest('.dot');
        if (dot) {
            estado[id].index = Number(dot.dataset.slide);
            actualizar(id);
            return;
        }
        if (e.target.closest('.carousel-btn.prev')) mover(id, -1);
        if (e.target.closest('.carousel-btn.next')) mover(id, 1);
    });
});

// Solo se cargan los carruseles que existan en esta página
CARRUSELES.filter(c => document.querySelector(`[data-carousel="${c.id}"]`))
          .forEach(cargarCarrusel);

// ══════════════════ OFERTA FORMATIVA (desde JSON) ══════════════════

function campo(label, valor) {
    if (!valor) return '';
    return `<div><p>${esc(label)}</p><p>${esc(valor)}</p></div>`;
}

function renderCurso(curso) {
    const meta = [
        campo('Dirigido a', curso.dirigido_a),
        campo('Horarios', curso.horarios),
        campo('Inicio', curso.inicio),
        campo('Disponibilidad', curso.disponibilidad)
    ].join('');

    const descarga = curso.contenido_url
        ? `<a class="course-dl" href="${esc(curso.contenido_url)}" target="_blank" rel="noopener noreferrer">
               <svg class="ico ico-md" aria-hidden="true"><use href="#i-download"/></svg> Descargar contenido
           </a>`
        : `<span class="course-unavailable">Contenido no disponible por ahora</span>`;

    return `
        <details class="course">
            <summary>
                <span class="course-num">${esc(curso.numero)}</span>
                <span class="course-title">${esc(curso.titulo)}</span>
            </summary>
            <div class="course-body">
                ${curso.resumen ? `<p>${esc(curso.resumen)}</p>` : ''}
                ${meta ? `<div class="course-meta">${meta}</div>` : ''}
                ${descarga}
            </div>
        </details>`;
}

function renderPrograma(p) {
    const facts = [
        p.duracion     ? { l: 'Duración', v: p.duracion } : null,
        p.costo        ? { l: 'Costo', v: p.costo } : null,
        p.inicio       ? { l: 'Inicio', v: p.inicio } : null,
        p.metodologias ? { l: 'Metodología', v: p.metodologias } : null
    ].filter(Boolean)
     .map(f => `<div class="fact"><p class="fact-label">${esc(f.l)}</p><p class="fact-value">${esc(f.v)}</p></div>`)
     .join('');

    return `
        <div class="program reveal">
            <div class="program-head">
                <div>
                    <h3>${esc(p.titulo)}</h3>
                    <p class="program-objective">${esc(p.objetivo || '')}</p>
                </div>
                <div class="program-facts">${facts}</div>
            </div>
            ${p.aviso ? `<div class="program-notice"><svg class="ico ico-md" aria-hidden="true"><use href="#i-alert"/></svg><span>${esc(p.aviso)}</span></div>` : ''}
            <div class="course-list">${(p.cursos || []).map(renderCurso).join('')}</div>
        </div>`;
}

(async function cargarOferta() {
    const cont = document.getElementById('ofertaContainer');
    if (!cont) return;          // la página no muestra la oferta
    try {
        const res = await fetch('../../data/cedic-cursos.json');
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const data = await res.json();

        cont.innerHTML = (data.programas || []).map(renderPrograma).join('');

        // Reactiva el reveal sobre los bloques recién inyectados
        cont.querySelectorAll('.reveal').forEach(el => observer.observe(el));

        // Antes acá se avisaba por consola de los títulos deducidos y las fichas
        // incompletas, leyendo campos `_revisar` del JSON. Esos campos salieron
        // del JSON porque el navegador lo descarga entero y eran notas internas.
        // El listado vive ahora en docs/cedic-pendientes.md.
    } catch (error) {
        console.error('[oferta] no se pudo cargar cedic-cursos.json:', error);
        cont.innerHTML = `<div class="offer-error">
            No pudimos cargar la oferta formativa. Escríbenos a
            <a href="mailto:evem@unet.edu.ve">evem@unet.edu.ve</a> para más información.
        </div>`;
    }
})();

// ══════════════════ TEASER DE ACTIVIDADES (landing) ══════════════════
// Cada tarjeta declara su carpeta en data-cover y recibe como portada
// la primera imagen de esa carpeta. Reusa el mismo endpoint que los
// carruseles, así que no hay que mantener rutas de imagen a mano.
(function portadasTeaser() {
    const portadas = document.querySelectorAll('.teaser-cover[data-cover]');
    if (!portadas.length) return;

    portadas.forEach(async el => {
        const carpeta = el.dataset.cover;
        try {
            const res = await fetch(`../../backend/api.php?action=get_carousel_images&folder=${carpeta}`);
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const imgs = await res.json();
            if (!Array.isArray(imgs) || !imgs.length) return;   // se queda el degradado

            // Precargar antes de mostrar, para que no aparezca a medio pintar.
            // encodeURI codifica las comillas del nombre de archivo, que si no
            // podrían cerrar el url("...") e inyectar CSS arbitrario.
            const img = new Image();
            img.onload = () => {
                el.style.backgroundImage = `url("${encodeURI(imgs[0])}")`;
                el.classList.add('cargada');
            };
            img.src = imgs[0];
        } catch (error) {
            console.warn(`[portada:${carpeta}] sin imagen de portada:`, error);
        }
    });
})();

// ══════════════════ TEASER DE FORMACIÓN (landing) ══════════════════
// Resume los programas del mismo JSON que alimenta formacion.php:
// una sola fuente de datos, cero contenido duplicado.
(async function programasTeaser() {
    const cont = document.getElementById('programasTeaser');
    if (!cont) return;

    try {
        const res = await fetch('../../data/cedic-cursos.json');
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const data = await res.json();

        cont.innerHTML = (data.programas || []).map(p => {
            const n = (p.cursos || []).length;
            const pills = [p.duracion, p.costo]
                .filter(Boolean)
                .map(v => `<span class="pill">${esc(v)}</span>`)
                .join('');
            // El objetivo completo es largo para una tarjeta: primera frase
            const resumen = (p.objetivo || '').split(/(?<=\.)\s/)[0];

            return `
                <a href="formacion.php#${esc(p.id)}" class="program-card">
                    <span class="count">${n}<small>${n === 1 ? 'programa' : 'cursos'}</small></span>
                    <h3>${esc(p.titulo)}</h3>
                    <p>${esc(resumen)}</p>
                    <div class="pills">${pills}</div>
                    <span class="ver">Ver detalle <svg class="ico arrow" aria-hidden="true"><use href="#i-arrow-right"/></svg></span>
                </a>`;
        }).join('');
    } catch (error) {
        console.error('[programas] no se pudo cargar cedic-cursos.json:', error);
        cont.innerHTML = `<div class="offer-error">
            No pudimos cargar los programas. Consultá la
            <a href="formacion.php">oferta formativa</a>.
        </div>`;
    }
})();

// ── Biografías expandibles ──
// Solo muestra el botón si la bio realmente está recortada.
document.querySelectorAll('.member').forEach(card => {
    const bio = card.querySelector('.member-bio');
    const btn = card.querySelector('.bio-toggle');
    if (!bio || !btn) return;

    if (bio.scrollHeight <= bio.clientHeight + 2) {
        btn.style.display = 'none';
        return;
    }

    btn.addEventListener('click', () => {
        const expandida = bio.classList.toggle('clamped') === false;
        btn.textContent = expandida ? 'Leer menos' : 'Leer más';
    });
});