class EVEMApiClient {
    constructor() {
        // 1. Detectar si estamos dentro de la carpeta "pages"
        // (Verifica si la URL del navegador contiene "/pages/")
        const isPagesFolder = window.location.pathname.includes('/pages/');

        // 2. Definir el prefijo
        // Si estamos en pages, usamos "../" para salir. Si no, usamos vacio "".
        const prefix = isPagesFolder ? '../' : '';

        // 3. Construir la ruta final
        this.baseURL = `${prefix}backend/api.php?action=`;

        console.log('API Configurada en:', this.baseURL); // Para depurar
    }

    async request(endpoint, options = {}) {
        // endpoint llega como "/courses", le quitamos la barra "/"
        const action = endpoint.replace('/', '');
        const url = `${this.baseURL}${action}`;

        try {
            const response = await fetch(url, {
                ...options,
                headers: {
                    'Content-Type': 'application/json',
                    ...options.headers,
                },
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || `Error ${response.status}: Algo salió mal`);
            }

            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    // Obtener lista de cursos
    async getCourses() {
        return this.request('/courses');
    }

    // Registrar participante
    async registerParticipant(formData) {
        return this.request('/register', {
            method: 'POST',
            body: JSON.stringify(formData)
        });
    }
}

// Función para cargar cursos en courses.html
async function loadCourses() {
    const container = document.getElementById('courses-container');
    if (!container) return; // Si no estamos en la página de cursos, no hace nada

    try {
        // Asegúrate de que esta URL apunte a tu backend correctamente
        const response = await fetch('../backend/api.php?action=courses'); 
        const courses = await response.json();

        // Limpiamos el mensaje de "Cargando..."
        container.innerHTML = '';

        // Recorremos cada curso que viene de la base de datos
        courses.forEach(course => {
            // 1. Cálculos matemáticos para la barra de progreso
            const capacity = Number.parseInt(course.max_capacity, 10) || 40;
            const enrolled = Number.parseInt(course.current_enrollment, 10) || 0;
            const available = capacity - enrolled;
            const percentage = Math.round((enrolled / capacity) * 100);
            
            // 2. Definir colores y textos según los cupos
            const isFull = available <= 0;
            const badgeText = isFull ? 'Agotado' : 'Cupos Libres';
            const badgeColor = isFull ? 'var(--color-error)' : 'var(--color-success)';
            const barColor = isFull ? 'var(--color-error)' : 'var(--color-success)';

            // 3. (Opcional) Asignar un icono divertido según el título del curso
            let icon = '📘';
            const titleLower = course.title.toLowerCase();
            if(titleLower.includes('geometría')) icon = '📐';
            else if(titleLower.includes('cálculo') || titleLower.includes('derivada')) icon = '📈';
            else if(titleLower.includes('ia') || titleLower.includes('inteligencia')) icon = '🤖';
            else if(titleLower.includes('fracciones') || titleLower.includes('operaciones')) icon = '➗';

            // 4. Armar la tarjeta HTML idéntica a nuestro diseño moderno
            const cardHTML = `
                <article class="modern-course-card">
                    <div class="card-badge" style="background-color: ${badgeColor};">${badgeText}</div>
                    <div class="card-icon-header">${icon}</div>
                    <h3 class="modern-course-title">${course.title}</h3>
                    <p class="modern-course-teacher">${course.instructor}</p>
                    <p class="modern-course-institution">Curso EVEM 2026</p>
                    
                    <p style="font-size: 0.9rem; color: var(--color-text-secondary); margin: 15px 0;">
                        ${course.description || 'Curso intensivo de 4 días de duración.'}
                    </p>

                    <div class="course-stats">
                        <div class="stat-row">
                            <span>Cupos disponibles:</span>
                            <span>${Math.max(available, 0)} / ${capacity}</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-fill" style="width: ${percentage}%; background-color: ${barColor};"></div>
                        </div>
                    </div>
                </article>
            `;

            // Insertar la tarjeta en la página
            container.innerHTML += cardHTML;
        });

    } catch (error) {
        console.error('Error al cargar la base de datos:', error);
        container.innerHTML = `
            <div style="text-align: center; grid-column: 1 / -1; padding: 40px; color: var(--color-error);">
                <h3>Error al conectar con el servidor 🚨</h3>
                <p>Por favor, revisa que XAMPP esté encendido y la base de datos activa.</p>
            </div>
        `;
    }
}

// Ejecutar la función cuando la página termine de cargar
document.addEventListener('DOMContentLoaded', () => {
    loadCourses();
});