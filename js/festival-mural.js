// js/festival-mural.js — Mural de proyectos del Festival, compartido
// entre festival.html y festival-certificado.html: un solo componente,
// no dos implementaciones. Se monta dentro de cualquier elemento con
// [data-festival-mural]. El VALOR del atributo elige el modo:
//   data-festival-mural="carousel"  → pista infinita animada (festival.html)
//   data-festival-mural (sin valor) → grid estático (festival-certificado.html,
//                                     sin tocar — sigue siendo el default)
//
// Requiere que el host haya cargado js/security.js ANTES que este
// script (usa el escapeHTML() global, no define uno propio).
(function () {
  const mounts = document.querySelectorAll("[data-festival-mural]");
  if (!mounts.length) return;

  // prefers-reduced-motion cae siempre a grid, sin importar lo que pida
  // el host: "fila estática sin autoplay" es exactamente lo que ya hace
  // el modo grid (todas las tarjetas visibles, sin animación, sin
  // depender de scroll horizontal) — no una versión más lenta del
  // carrusel.
  const reduceMotion = window.matchMedia(
    "(prefers-reduced-motion: reduce)",
  ).matches;

  function cardHTML(p) {
    return `
      <div class="project-card">
        <div class="name">${escapeHTML(p.team_name)}</div>
        <div class="inst">${escapeHTML(p.institution)}</div>
      </div>
    `;
  }

  function renderGrid(mount, proyectos) {
    mount.innerHTML = `<div class="projects-grid" aria-live="polite">${proyectos.map(cardHTML).join("")}</div>`;
  }

  // Loop infinito con CSS puro: la pista contiene el set de tarjetas
  // DOS veces seguidas y se anima con translateX(-50%) — como las dos
  // mitades son idénticas, el instante en que termina de recorrer la
  // primera mitad es visualmente indistinguible del inicio, así que el
  // salto de vuelta a 0% no se nota. El segundo set se marca
  // aria-hidden="true" para que un lector de pantalla no anuncie cada
  // proyecto dos veces. display:contents en cada "set" evita que el
  // envoltorio agregue su propia caja/gap — las tarjetas quedan como
  // hijos flex directos de .festival-mural-track, con el mismo gap
  // uniforme en toda la pista, incluida la costura entre ambos sets.
  function renderCarousel(mount, proyectos) {
    const cardsHTML = proyectos.map(cardHTML).join("");
    // Duración proporcional a la cantidad de tarjetas: la velocidad
    // visual (px/seg) se mantiene estable sin importar si hay 6
    // proyectos o 60, en vez de acelerarse/enlentecerse con el tiempo.
    const duration = Math.max(18, proyectos.length * 3.2).toFixed(1);
    mount.innerHTML = `
      <div class="festival-mural-carousel">
        <div class="festival-mural-track" style="--marquee-duration:${duration}s">
          <div class="festival-mural-set">${cardsHTML}</div>
          <div class="festival-mural-set" aria-hidden="true">${cardsHTML}</div>
        </div>
      </div>
    `;
  }

  fetch("../../backend/api.php?action=get_festival_proyectos_publico")
    .then((res) => res.json())
    .then((data) => {
      const proyectos = data.proyectos ?? [];
      mounts.forEach((mount) => {
        if (!proyectos.length) {
          mount.innerHTML =
            '<div class="festival-mural-empty">Todavía no hay proyectos confirmados para mostrar.</div>';
          return;
        }
        const wantsCarousel = mount.dataset.festivalMural === "carousel";
        if (wantsCarousel && !reduceMotion) {
          renderCarousel(mount, proyectos);
        } else {
          renderGrid(mount, proyectos);
        }
      });
    })
    .catch(() => {
      mounts.forEach((mount) => {
        mount.innerHTML =
          '<div class="festival-mural-error">No se pudo cargar la lista de proyectos en este momento.</div>';
      });
    });
})();
