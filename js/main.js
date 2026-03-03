// js/main.js

class EVEMApp {
    constructor() {
        console.log("🎓 EVEM 2025 - Inicializando...");
        
        // 1. AQUÍ CREAMOS LA CONEXIÓN (IMPORTANTE)
        this.api = new EVEMApiClient(); 
        
        this.form = document.getElementById('registrationForm');
        this.init();
    }

    async init() {
        // Inicializar notificaciones
        this.setupNotifications();

        // Cargar cursos desde la Base de Datos
        await this.loadDatabaseCourses();

        // Escuchar el envío del formulario
        if (this.form) {
            this.form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }

        console.log("✅ Aplicación inicializada correctamente");
    }

    // --- CARGAR CURSOS ---
    async loadDatabaseCourses() {
        const courseSelect = document.getElementById('course');
        // Si no estamos en la página de registro, salimos
        if (!courseSelect) return; 

        try {
            // CORRECCIÓN AQUÍ: Usamos 'this.api' en vez de solo 'api'
            const courses = await this.api.getCourses();
            
            // Limpiar opciones viejas
            courseSelect.innerHTML = '<option value="">Seleccione un curso...</option>';

            courses.forEach(course => {
                const option = document.createElement('option');
                option.value = course.title;
                // Mostramos Título y Cupos disponibles
                const cupos = course.max_capacity - course.current_enrollment;
                option.textContent = `${course.title} (Cupos: ${cupos})`;
                
                // Si está lleno, deshabilitar
                if (cupos <= 0) {
                    option.disabled = true;
                    option.textContent += " - AGOTADO";
                }
                
                courseSelect.appendChild(option);
            });
        } catch (error) {
            console.error("Error cargando cursos:", error);
            this.showNotification("No se pudieron cargar los cursos. Revise la conexión.", "error");
        }
    }

    // --- ENVIAR FORMULARIO ---
    async handleFormSubmit(e) {
        e.preventDefault();
        
        const submitBtn = this.form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        try {
            submitBtn.disabled = true;
            submitBtn.textContent = "Enviando...";

            // Recopilar datos del formulario
            const formData = new FormData(this.form);
            const data = Object.fromEntries(formData.entries());

            // Checkbox fixes (convertir "on" a true/false si es necesario, 
            // aunque el PHP ya lo maneja, es bueno asegurarnos)
            data.wantsNewsletter = this.form.querySelector('[name="wantsNewsletter"]')?.checked;
            data.acceptedTerms = this.form.querySelector('[name="terms"]')?.checked;

            // CORRECCIÓN AQUÍ TAMBIÉN: 'this.api'
            const response = await this.api.registerParticipant(data);

            this.showNotification(`¡Inscripción Exitosa! ID: ${response.id}`, 'success');
            this.form.reset();
            
            // Recargar cursos para actualizar cupos
            await this.loadDatabaseCourses();

        } catch (error) {
            console.error(error);
            this.showNotification(error.message || "Error al inscribir", "error");
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    }

    // --- SISTEMA DE NOTIFICACIONES (Toast) ---
    setupNotifications() {
        const container = document.createElement('div');
        container.id = 'notification-container';
        container.style.cssText = `
            position: fixed; top: 20px; right: 20px; z-index: 9999;
        `;
        document.body.appendChild(container);
    }

    showNotification(message, type = 'info') {
        const container = document.getElementById('notification-container');
        const toast = document.createElement('div');
        
        // Colores según tipo
        const bg = type === 'success' ? '#2ecc71' : (type === 'error' ? '#e74c3c' : '#3498db');

        toast.style.cssText = `
            background: ${bg}; color: white; padding: 15px 25px; 
            margin-bottom: 10px; border-radius: 5px; box-shadow: 0 3px 6px rgba(0,0,0,0.16);
            transform: translateX(120%); transition: transform 0.3s ease; font-family: sans-serif;
        `;
        toast.textContent = message;

        container.appendChild(toast);
        
        // Animar entrada
        requestAnimationFrame(() => {
            toast.style.transform = 'translateX(0)';
        });

        // Quitar después de 3 segundos
        setTimeout(() => {
            toast.style.transform = 'translateX(120%)';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }
}

// Iniciar la app cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new EVEMApp();
});