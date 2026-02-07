// Main - Inicialización principal de la aplicación

class EVEMApp {
    constructor() {
        this.data = EVEM_DATA; // Mantenemos data estática para textos
        this.init();
    }

    init() {
        console.log('🎓 EVEM 2025 - Inicializando...');
        this.updateDynamicContent();
        this.setupFormHandlers();
        this.setupEventListeners();
        
        // NUEVO: Cargar cursos desde la base de datos si estamos en la página de contacto
        this.loadDatabaseCourses(); 
        
        console.log('✅ Aplicación inicializada correctamente');
    }

    // --- NUEVA FUNCIÓN: Cargar cursos desde MySQL ---
    async loadDatabaseCourses() {
        const courseSelect = document.getElementById('course');
        // Si no existe el select, no estamos en la página de contacto, salimos.
        if (!courseSelect) return;

        try {
            // Usamos la API que creamos
            const courses = await api.getCourses();
            
            // Limpiamos las opciones hardcodeadas del HTML
            courseSelect.innerHTML = '<option value="">Seleccione un curso...</option>';

            courses.forEach(course => {
                const isFull = course.current_enrollment >= course.max_capacity;
                const option = document.createElement('option');
                // Usamos el título tal cual viene de la DB
                option.value = course.title; 
                option.textContent = `${course.title} - ${course.professor_name} ${isFull ? '(AGOTADO)' : ''}`;
                
                if (isFull) option.disabled = true;
                
                courseSelect.appendChild(option);
            });
            console.log('📡 Cursos cargados desde Base de Datos');
        } catch (error) {
            console.error('Error cargando cursos:', error);
            this.showNotification('Error conectando con el servidor de cursos', 'error');
        }
    }

    // Actualizar contenido dinámico (Textos estáticos)
    updateDynamicContent() {
        this.updateEventInfo();
        this.updateContactInfo();
    }

    updateEventInfo() {
        const dateElements = document.querySelectorAll('[data-event-dates]');
        dateElements.forEach(el => el.textContent = this.data.event.dates.display);

        const locationElements = document.querySelectorAll('[data-event-location]');
        locationElements.forEach(el => el.textContent = `${this.data.event.location.city}, Estado ${this.data.event.location.state}`);
    }

    updateContactInfo() {
        const emailElements = document.querySelectorAll('[data-contact-email]');
        emailElements.forEach(el => {
            el.textContent = this.data.event.contact.email;
            if (el.tagName === 'A') el.href = `mailto:${this.data.event.contact.email}`;
        });

        const phoneElements = document.querySelectorAll('[data-contact-phone]');
        phoneElements.forEach(el => {
            el.textContent = this.data.event.contact.phone;
            if (el.tagName === 'A') el.href = `tel:${this.data.event.contact.phone.replace(/\s/g, '')}`;
        });
    }

    setupFormHandlers() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            // Usamos una función async para poder esperar la respuesta del servidor
            form.addEventListener('submit', async (e) => await this.handleFormSubmit(e));
        });
    }

    // --- FUNCIÓN MODIFICADA: Enviar a MySQL ---
    async handleFormSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Feedback visual de carga
        const originalBtnText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = "Enviando...";

        try {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            // ADAPTACIÓN: El backend espera 'coursePreference', el HTML tiene 'course'
            // Mapeamos los datos para que coincidan con la base de datos
            const backendData = {
                cedula: data.cedula,
                fullName: data.fullName,
                birthDate: data.birthDate,
                email: data.email,
                phone: data.phone,
                institution: data.institution,
                state: data.state,
                city: data.city,
                position: data.position,
                experienceYears: data.experience, // El HTML dice 'experience', backend espera 'experienceYears'
                coursePreference: data.course,    // El HTML dice 'course', backend espera 'coursePreference'
                expectations: data.expectations,
                previousParticipation: data.previousParticipation,
                wantsNewsletter: data.newsletter === 'yes',
                acceptedTerms: data.terms === 'yes'
            };

            // Enviamos al Backend
            console.log('📤 Enviando datos al servidor:', backendData);
            const response = await api.registerParticipant(backendData);
            
            // Éxito
           this.showNotification(`¡Inscripción Exitosa! ID: ${response.id}`, 'success');
            form.reset();
            
            // Recargar cursos por si se llenó alguno
            this.loadDatabaseCourses();

        } catch (error) {
            console.error('Error en registro:', error);
            // Mostrar mensaje de error del backend (ej: "Ya existe esa cédula")
            let errorMsg = error.message;
            if(errorMsg === 'Failed to fetch') errorMsg = "No hay conexión con el servidor";
            
            this.showNotification(errorMsg, 'error');
        } finally {
            // Restaurar botón
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    }

    // Mostrar notificaciones (Mantenemos tu diseño original)
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
            border-left: 4px solid ${this.getColorByType(type)};
        `;

        document.body.appendChild(notification);

        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        });

        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
    }

    getNotificationIcon(type) {
        const icons = { success: '✓', error: '✗', warning: '⚠', info: 'ℹ' };
        return icons[type] || icons.info;
    }

    getColorByType(type) {
        const colors = { success: '#27AE60', error: '#E74C3C', warning: '#F39C12', info: '#3498DB' };
        return colors[type] || '#333';
    }

    setupEventListeners() {
        document.querySelectorAll('a[href="#"]').forEach(link => {
            link.addEventListener('click', (e) => e.preventDefault());
        });
    }
}

// Estilos de notificación
const notificationStyles = document.createElement('style');
notificationStyles.textContent = `
    @keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(400px); opacity: 0; } }
    .notification-content { display: flex; align-items: center; gap: 0.5rem; flex: 1; }
    .notification-icon { font-size: 1.5rem; font-weight: bold; }
    .notification-success .notification-icon { color: #27AE60; }
    .notification-error .notification-icon { color: #E74C3C; }
    .notification-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #999; }
`;
document.head.appendChild(notificationStyles);

// Inicializar
let app;
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => { app = new EVEMApp(); });
} else {
    app = new EVEMApp();
}
window.EVEMApp = EVEMApp;