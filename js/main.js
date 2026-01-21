// Main - Inicialización principal de la aplicación

class EVEMApp {
    constructor() {
        this.data = EVEM_DATA;
        this.init();
    }

    init() {
        console.log('🎓 EVEM 2025 - Inicializando...');
        this.updateDynamicContent();
        this.setupFormHandlers();
        this.setupEventListeners();
        console.log('✅ Aplicación inicializada correctamente');
    }

    // Actualizar contenido dinámico
    updateDynamicContent() {
        this.updateEventInfo();
        this.updateContactInfo();
    }

    updateEventInfo() {
        // Actualizar fechas dinámicamente
        const dateElements = document.querySelectorAll('[data-event-dates]');
        dateElements.forEach(el => {
            el.textContent = this.data.event.dates.display;
        });

        // Actualizar ubicación
        const locationElements = document.querySelectorAll('[data-event-location]');
        locationElements.forEach(el => {
            el.textContent = `${this.data.event.location.city}, Estado ${this.data.event.location.state}`;
        });
    }

    updateContactInfo() {
        // Actualizar email
        const emailElements = document.querySelectorAll('[data-contact-email]');
        emailElements.forEach(el => {
            el.textContent = this.data.event.contact.email;
            if (el.tagName === 'A') {
                el.href = `mailto:${this.data.event.contact.email}`;
            }
        });

        // Actualizar teléfono
        const phoneElements = document.querySelectorAll('[data-contact-phone]');
        phoneElements.forEach(el => {
            el.textContent = this.data.event.contact.phone;
            if (el.tagName === 'A') {
                el.href = `tel:${this.data.event.contact.phone.replace(/\s/g, '')}`;
            }
        });
    }

    // Manejadores de formularios
    setupFormHandlers() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        });
    }

    handleFormSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        
        console.log('📝 Formulario enviado:', Object.fromEntries(formData));
        
        // Aquí se integraría con un backend real
        this.showNotification('¡Formulario enviado correctamente!', 'success');
        form.reset();
    }

    // Mostrar notificaciones
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-icon">${this.getNotificationIcon(type)}</span>
                <span class="notification-message">${message}</span>
            </div>
            <button class="notification-close" aria-label="Cerrar">×</button>
        `;

        // Estilos de notificación
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 1rem;
            animation: slideIn 0.3s ease;
            max-width: 400px;
        `;

        document.body.appendChild(notification);

        // Cerrar al hacer click
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        });

        // Auto cerrar después de 5 segundos
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
    }

    getNotificationIcon(type) {
        const icons = {
            success: '✓',
            error: '✗',
            warning: '⚠',
            info: 'ℹ'
        };
        return icons[type] || icons.info;
    }

    // Event listeners globales
    setupEventListeners() {
        // Prevenir comportamiento por defecto de enlaces vacíos
        document.querySelectorAll('a[href="#"]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
            });
        });

        // Debug mode con Ctrl+Shift+D
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.shiftKey && e.key === 'D') {
                this.toggleDebugMode();
            }
        });

        // Detectar modo offline
        window.addEventListener('offline', () => {
            this.showNotification('Sin conexión a Internet', 'warning');
        });

        window.addEventListener('online', () => {
            this.showNotification('Conexión restaurada', 'success');
        });
    }

    // Modo debug
    toggleDebugMode() {
        document.body.classList.toggle('debug-mode');
        const isDebug = document.body.classList.contains('debug-mode');
        
        if (isDebug) {
            console.log('🐛 Modo debug activado');
            console.log('📊 Datos de la aplicación:', this.data);
        } else {
            console.log('🐛 Modo debug desactivado');
        }
    }

    // Utilidades
    static formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('es-ES', options);
    }

    static validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    static debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Agregar estilos de animación para notificaciones
const notificationStyles = document.createElement('style');
notificationStyles.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    .notification-content {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex: 1;
    }

    .notification-icon {
        font-size: 1.5rem;
        font-weight: bold;
    }

    .notification-success .notification-icon {
        color: var(--color-success, #27AE60);
    }

    .notification-error .notification-icon {
        color: var(--color-error, #E74C3C);
    }

    .notification-warning .notification-icon {
        color: var(--color-warning, #F39C12);
    }

    .notification-info .notification-icon {
        color: var(--color-info, #3498DB);
    }

    .notification-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #999;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .notification-close:hover {
        color: #333;
    }

    /* Debug mode styles */
    .debug-mode * {
        outline: 1px solid rgba(255, 0, 0, 0.3);
    }

    .debug-mode *:hover {
        outline: 2px solid rgba(255, 0, 0, 0.6);
    }

    @media (max-width: 768px) {
        .notification {
            left: 10px !important;
            right: 10px !important;
            max-width: calc(100% - 20px) !important;
        }
    }
`;
document.head.appendChild(notificationStyles);

// Inicializar aplicación cuando el DOM esté listo
let app;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        app = new EVEMApp();
    });
} else {
    app = new EVEMApp();
}

// Exportar para uso global
window.EVEMApp = EVEMApp;