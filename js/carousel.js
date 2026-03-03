// ============================================
// CARRUSEL HERO - Control de imágenes
// ============================================

class HeroCarousel {
    constructor() {
        this.slides = document.querySelectorAll('.hero-slide');
        this.indicators = document.querySelectorAll('.carousel-indicators .indicator');
        this.prevBtn = document.querySelector('.carousel-control.prev');
        this.nextBtn = document.querySelector('.carousel-control.next');
        this.currentSlide = 0;
        this.autoPlayInterval = null;
        this.autoPlayDelay = 5000; // 5 segundos

        if (this.slides.length > 0) {
            this.init();
        }
    }

    init() {
        // Event listeners para las flechas
        this.prevBtn?.addEventListener('click', () => {
            this.previousSlide();
            this.resetAutoPlay();
        });

        this.nextBtn?.addEventListener('click', () => {
            this.nextSlide();
            this.resetAutoPlay();
        });

        // Event listeners para los indicadores
        this.indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                this.goToSlide(index);
                this.resetAutoPlay();
            });
        });

        // Pausar al pasar el mouse
        const heroSection = document.querySelector('.hero-2026');
        if (heroSection) {
            heroSection.addEventListener('mouseenter', () => this.pauseAutoPlay());
            heroSection.addEventListener('mouseleave', () => this.startAutoPlay());
        }

        // Teclado (flechas izquierda/derecha)
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                this.previousSlide();
                this.resetAutoPlay();
            } else if (e.key === 'ArrowRight') {
                this.nextSlide();
                this.resetAutoPlay();
            }
        });

        // Touch swipe para móviles
        this.setupTouchControls(heroSection);

        // Iniciar autoplay
        this.startAutoPlay();
    }

    goToSlide(index) {
        // Remover active del slide actual
        this.slides[this.currentSlide].classList.remove('active');
        this.indicators[this.currentSlide].classList.remove('active');

        // Actualizar índice
        this.currentSlide = index;

        // Agregar active al nuevo slide
        this.slides[this.currentSlide].classList.add('active');
        this.indicators[this.currentSlide].classList.add('active');
    }

    nextSlide() {
        const next = (this.currentSlide + 1) % this.slides.length;
        this.goToSlide(next);
    }

    previousSlide() {
        const prev = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
        this.goToSlide(prev);
    }

    startAutoPlay() {
        this.autoPlayInterval = setInterval(() => {
            this.nextSlide();
        }, this.autoPlayDelay);
    }

    pauseAutoPlay() {
        if (this.autoPlayInterval) {
            clearInterval(this.autoPlayInterval);
            this.autoPlayInterval = null;
        }
    }

    resetAutoPlay() {
        this.pauseAutoPlay();
        this.startAutoPlay();
    }

    // Touch controls para móviles
    setupTouchControls(element) {
        if (!element) {
            return;
        }

        let touchStartX = 0;
        let touchEndX = 0;

        element.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        element.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            this.handleSwipe(touchStartX, touchEndX);
        }, { passive: true });
    }

    handleSwipe(touchStartX, touchEndX) {
        const swipeThreshold = 50;

        if (touchEndX < touchStartX - swipeThreshold) {
            // Swipe left - siguiente
            this.nextSlide();
            this.resetAutoPlay();
        }

        if (touchEndX > touchStartX + swipeThreshold) {
            // Swipe right - anterior
            this.previousSlide();
            this.resetAutoPlay();
        }
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    globalThis.heroCarousel = new HeroCarousel();
});
