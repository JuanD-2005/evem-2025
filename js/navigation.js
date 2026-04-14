// Navigation - Lógica de navegación y menú

class Navigation {
    constructor() {
        this.navToggle = document.querySelector('.nav-toggle');
        this.navMobileMenu = document.querySelector('.nav-mobile-menu');
        this.navButtons = document.querySelectorAll('.nav-button, .nav-mobile-link');
        this.navbar = document.querySelector('.navbar');

        this.init();
    }

    init() {
        this.setupMobileMenu();
        this.setupSmoothScroll();
        this.setupActiveLinks();
        this.setupStickyNavbar();
    }

    // Menú móvil
    setupMobileMenu() {
        if (!this.navToggle) return;

        this.navToggle.addEventListener('click', () => {
            this.toggleMenu();
        });

        // Cerrar menú al hacer click en un enlace
        this.navButtons.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    this.closeMenu();
                }
            });
        });

        // Cerrar menú al hacer click fuera
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && 
                this.navMobileMenu && 
                this.navMobileMenu.classList.contains('active') &&
                !this.navMobileMenu.contains(e.target) &&
                !this.navToggle.contains(e.target)) {
                this.closeMenu();
            }
        });
    }

    toggleMenu() {
        if (!this.navMobileMenu) return;

        this.navMobileMenu.classList.toggle('active');
        this.navToggle?.classList.toggle('active');

        // Prevenir scroll cuando el menú está abierto
        if (this.navMobileMenu.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }

    closeMenu() {
        if (!this.navMobileMenu) return;

        this.navMobileMenu.classList.remove('active');
        this.navToggle?.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Scroll suave
    setupSmoothScroll() {
        this.navButtons.forEach(link => {
            const href = link.getAttribute('href');
            
            // Solo aplicar a anchors internos
            if (href?.startsWith('#')) {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetId = href.substring(1);
                    const targetElement = document.getElementById(targetId);
                    
                    if (targetElement) {
                        const navbarHeight = this.navbar.offsetHeight;
                        const targetPosition = targetElement.offsetTop - navbarHeight;
                        
                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            }
        });
    }

    // Enlaces activos según scroll
    setupActiveLinks() {
        // 1. Lógica original para el scroll de las secciones (Quiénes somos, Comité)
        const sections = document.querySelectorAll('section[id]');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.setActiveLink(entry.target.getAttribute('id'));
                }
            });
        }, { rootMargin: '-100px 0px -66%', threshold: 0 });

        sections.forEach(section => observer.observe(section));

        // 2. NUEVA MAGIA: Auto-detectar la página actual para los botones móviles
        const currentPath = window.location.pathname; // Ej: /pages/courses.html

        this.navButtons.forEach(link => {
            const href = link.getAttribute('href');
            if (!href) return;

            // Extraemos solo el nombre del archivo (ej: 'courses.html')
            const fileName = href.split('/').pop();

            // Si la URL del navegador contiene el nombre del archivo de este botón...
            if (fileName && fileName !== 'index.html' && currentPath.includes(fileName)) {
                link.classList.add('active-page'); // Lo dejamos encendido
            }
            // Regla especial para el inicio
            else if ((currentPath.endsWith('/') || currentPath.endsWith('index.html')) && href.includes('index.html')) {
                // Opcional: Puedes encender la casita (Comité) si estás en el index
                if (href.includes('#comite')) {
                    link.classList.add('active-page');
                }
            }
        });
    }

    setActiveLink(id) {
        this.navButtons.forEach(link => {
            const href = link.getAttribute('href');

            // Guard: solo tocamos .style si existe el enlace y apunta a un anchor
            if (!href) return;

            if (href === `#${id}`) {
                link.style.borderBottomColor = 'var(--color-secondary)';
                return;
            }

            if (href.startsWith('#')) {
                link.style.borderBottomColor = 'transparent';
                return;
            }

            link.style.borderBottomColor = '';
        });
    }

    // Navbar con sombra al hacer scroll
    setupStickyNavbar() {
        // Guard principal: si no hay navbar en esta página, salimos
        if (!this.navbar) return;

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;

            // Agregar/quitar sombra
            if (currentScroll > 10) {
                this.navbar.style.boxShadow = 'var(--shadow-lg)';
            } else {
                this.navbar.style.boxShadow = 'var(--shadow-md)';
            }
        });
    }
}

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        globalThis.__navigationInstance = new Navigation();
    });
} else {
    globalThis.__navigationInstance = new Navigation();
}
