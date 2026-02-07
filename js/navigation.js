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
        this.navToggle.classList.toggle('active');
        
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
        this.navToggle.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Scroll suave
    setupSmoothScroll() {
        this.navButtons.forEach(link => {
            const href = link.getAttribute('href');
            
            // Solo aplicar a anchors internos
            if (href && href.startsWith('#')) {
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
        const sections = document.querySelectorAll('section[id]');
        
        const observerOptions = {
            rootMargin: '-100px 0px -66%',
            threshold: 0
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const id = entry.target.getAttribute('id');
                    this.setActiveLink(id);
                }
            });
        }, observerOptions);

        sections.forEach(section => observer.observe(section));
    }

    setActiveLink(id) {
        this.navButtons.forEach(link => {
            if (link.getAttribute('href') === `#${id}`) {
                link.style.borderBottomColor = 'var(--color-secondary)';
            } else if (link.getAttribute('href')?.startsWith('#')) {
                link.style.borderBottomColor = 'transparent';
            }
        });
    }

    // Navbar con sombra al hacer scroll
    setupStickyNavbar() {
        let lastScroll = 0;
        
        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            
            // Agregar/quitar sombra
            if (currentScroll > 10) {
                this.navbar.style.boxShadow = 'var(--shadow-lg)';
            } else {
                this.navbar.style.boxShadow = 'var(--shadow-md)';
            }
            
            lastScroll = currentScroll;
        });
    }
}

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new Navigation();
    });
} else {
    new Navigation();
}