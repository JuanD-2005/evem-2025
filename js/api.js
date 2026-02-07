class EVEMApiClient {
    constructor() {
        // Apunta a tu servidor Node.js local
        this.baseURL = 'http://localhost:3000/api';
    }

    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        };

        try {
            const response = await fetch(url, config);
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.error || 'Error desconocido en el servidor');
            }
            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    // --- MÉTODOS ---

    // Obtener cursos
    async getCourses() {
        return this.request('/courses');
    }

    // Registrar participante
    async registerParticipant(userData) {
        return this.request('/register', {
            method: 'POST',
            body: JSON.stringify(userData)
        });
    }
}

// Exportar instancia global
const api = new EVEMApiClient();