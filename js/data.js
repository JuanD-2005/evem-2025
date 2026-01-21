// Data - Datos de la aplicación (cursos, profesores, etc.)

const EVEM_DATA = {
    event: {
        name: "XXVII Escuela Venezolana para la Enseñanza de la Matemática",
        year: 2025,
        edition: "XXVII",
        dates: {
            start: "2025-09-08",
            end: "2025-09-11",
            display: "del 08 al 11 de Septiembre 2025"
        },
        location: {
            university: "Universidad Nacional Experimental del Táchira",
            shortName: "UNET",
            city: "San Cristóbal",
            state: "Táchira",
            venue: "Campus Universitario Paramillo",
            address: "Av. Universidad, Paramillo, San Cristóbal"
        },
        contact: {
            email: "evem.tachira@gmail.com",
            phone: "+58 (276) 353-0422",
            organizingUniversity: "Universidad de Los Andes (ULA)"
        }
    },

    courses: [
        {
            id: 1,
            title: "Fracciones",
            professor: "Prof. Pedro Infante",
            institution: "LUZ",
            description: "Estrategias innovadoras para la enseñanza de fracciones en educación media. Abordaje conceptual y metodológico para superar las dificultades comunes en el aprendizaje de operaciones con fracciones.",
            icon: "📊",
            duration: "4 días",
            level: "Intermedio"
        },
        {
            id: 2,
            title: "Geometría",
            professor: "Prof. Luis Astorga",
            institution: "ULA",
            description: "Exploración profunda de conceptos geométricos y su enseñanza efectiva. Incluye geometría plana, espacial y aplicaciones prácticas usando herramientas tradicionales y digitales.",
            icon: "📐",
            duration: "4 días",
            level: "Todos los niveles"
        },
        {
            id: 3,
            title: "Operaciones Básicas",
            professor: "Prof. Arístides Arellán",
            institution: "ULA",
            description: "Fundamentos de las operaciones aritméticas y su enseñanza significativa. Estrategias para desarrollar el sentido numérico y el razonamiento matemático desde las bases.",
            icon: "➗",
            duration: "4 días",
            level: "Básico"
        },
        {
            id: 4,
            title: "Matemática con Inteligencia Artificial",
            professor: "Prof. Pedro Méndez",
            institution: "LUZ",
            description: "Integración de herramientas de IA en la enseñanza de matemáticas. Uso de ChatGPT, Wolfram Alpha y otras plataformas para crear experiencias de aprendizaje innovadoras.",
            icon: "🤖",
            duration: "4 días",
            level: "Avanzado"
        },
        {
            id: 5,
            title: "Derivadas",
            professor: "Prof. Elías Velázco",
            institution: "UPTMa",
            description: "Introducción al cálculo diferencial para docentes de educación media. Conceptos fundamentales, interpretaciones geométricas y aplicaciones prácticas de las derivadas.",
            icon: "📈",
            duration: "4 días",
            level: "Avanzado"
        }
    ],

    committee: [
        {
            name: "Dr. José Ramírez",
            role: "Coordinador General",
            institution: "ULA",
            email: "jose.ramirez@ula.ve"
        },
        {
            name: "Prof. María González",
            role: "Coordinadora Académica",
            institution: "UNET",
            email: "maria.gonzalez@unet.edu.ve"
        },
        {
            name: "Prof. Carlos Pérez",
            role: "Coordinador de Logística",
            institution: "UNET",
            email: "carlos.perez@unet.edu.ve"
        }
    ],

    sponsors: [
        {
            name: "Universidad de Los Andes",
            type: "Organizador"
        },
        {
            name: "Universidad Nacional Experimental del Táchira",
            type: "Sede"
        },
        {
            name: "ASOVEMAT",
            type: "Colaborador"
        }
    ],

    testimonials: [
        {
            text: "La EVEM cambió mi forma de enseñar matemáticas. Los cursos son excelentes y el ambiente de aprendizaje es único.",
            author: "Prof. Ana Martínez",
            institution: "Liceo Bolivariano",
            year: 2024
        },
        {
            text: "Cada año espero con ansias la EVEM. Es una oportunidad invaluable de actualización y networking con colegas.",
            author: "Prof. Roberto Silva",
            institution: "U.E. Simón Bolívar",
            year: 2024
        }
    ],

    tribute: {
        name: "Darío Durán",
        title: "La Casa de Darío Durán",
        institution: "LUZ",
        legacy: "Su compromiso y pasión por la EVEM fueron evidentes desde el principio, y rápidamente la adoptó como propia, contribuyendo significativamente a su crecimiento y desarrollo en todos los espacios de formación docente.",
        message: "Hoy, al celebrar la EVEM en la UNET, honramos su memoria y nos inspiramos en su ejemplo para continuar construyendo un futuro brillante para la educación matemática en Venezuela."
    }
};

// Exportar datos para uso en otros módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EVEM_DATA;
}