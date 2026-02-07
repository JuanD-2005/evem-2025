# EVEM 2025 - Portal Web & Sistema de Inscripción

Portal oficial y sistema de gestión para la **XXVIII Escuela Venezolana para la Enseñanza de la Matemática**, organizado por la Universidad de Los Andes (ULA) y hospedado en la Universidad Nacional Experimental del Táchira (UNET).

> **Estado:** 🟢 Desarrollo Activo / Full Stack Funcional
> **Versión:** 2.0.0 (Integración Backend & Base de Datos)

## 🚀 Tecnologías Utilizadas

El proyecto ha evolucionado de un sitio estático a una aplicación web dinámica **Full Stack**.

### Frontend (Cliente)
- **HTML5 Semántico**: Estructura optimizada y accesible.
- **CSS3 Moderno**: Diseño responsivo, variables CSS, animaciones y diseño institucional.
- **JavaScript (Vanilla ES6+)**: Lógica del cliente, manejo del DOM y comunicación asíncrona (Fetch API).

### Backend (Servidor)
- **Node.js**: Entorno de ejecución de JavaScript.
- **Express.js**: Framework de servidor para manejo de rutas RESTful.
- **MySQL**: Base de datos relacional (gestionada vía XAMPP).
- **Seguridad**:
  - `cors`: Manejo de orígenes cruzados.
  - `helmet`: Protección de cabeceras HTTP.
  - `express-validator`: Sanitización y validación de datos de entrada.

---

## 📁 Estructura del Proyecto

```text
evem-2025/
├── index.html                  # Página de Inicio (Hero, Logos, CTA)
├── assets/
│   └── images/                 # Logos institucionales (UNET, ULA, EVEM)
├── css/
│   ├── layout.css              # Estilos estructurales (Header Institucional)
│   ├── components.css          # Botones animados y tarjetas
│   └── ...                     # Otros estilos base
├── js/
│   ├── api.js                  # Cliente API (Puente Frontend-Backend)
│   ├── main.js                 # Lógica de formularios y notificaciones
│   └── ...                     # Scripts de navegación y animación
├── pages/
│   ├── contact.html            # Formulario de Inscripción (Participante/Poster)
│   └── courses.html            # Catálogo dinámico de cursos
├── backend/                    # SERVIDOR API
│   ├── server.js               # Lógica del servidor y conexión a DB
│   ├── .env                    # Credenciales (No subir a repo público)
│   └── package.json            # Dependencias del proyecto
└── database/
   └── schema.sql              # Estructura de tablas SQL
```

## ⚙️ Instalación y Configuración

Para desplegar el proyecto en un entorno local o servidor de la UNET:

### 1. Base de Datos (MySQL)
- Iniciar Apache y MySQL en XAMPP.
- Crear una base de datos llamada `evem_2025`.
- Importar la estructura de tablas (Tabla `participants` actualizada con campos de Poster y courses).

### 2. Backend (Servidor Node)
- Abrir terminal en la carpeta `backend/`.
- Instalar dependencias:

```bash
npm install
```

- Configurar archivo `.env`:

```env
PORT=3000
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=evem_2025
```

- Iniciar el servidor:

```bash
# Para desarrollo
node server.js

# Para producción (Servidor UNET)
pm2 start server.js --name "evem-api"
```

### 3. Frontend
No requiere compilación. Abrir `index.html` en cualquier navegador moderno o servir con Apache/Nginx.

## 🔌 API Endpoints

El backend expone las siguientes rutas para el consumo del frontend:

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | /api/courses | Obtiene la lista de cursos activos y cupos disponibles. |
| POST | /api/register | Registra un nuevo usuario (valida cédula duplicada). |
| GET | /api/admin/participants | (Admin) Lista todos los inscritos. |

## ✨ Nuevas Funcionalidades (v2.0)

### 1. Modalidad de Inscripción Híbrida
El formulario ahora permite dos tipos de registro:
- **Participante Asistente**: Inscripción tradicional a cursos formativos.
- **Ponente (Poster)**: Habilita campos especiales para registrar el "Título del Trabajo" y "Resumen (Abstract)", manteniendo la opción de inscribirse en cursos.

### 2. Diseño Institucional (Header & Hero)
- Implementación de la barra de navegación oficial con logos de la UNET, ULA, EVEM y ENCOMAT.
- Diseño de "Pestaña Central" para el logo del evento.
- Hero Section rediseñada con identidad visual de la edición XXVIII.

### 3. Interacciones Modernas
- Botones con animación CSS avanzada ("Hover Reveal").
- Notificaciones flotantes (Toasts) para feedback de éxito/error en el registro.
- Validación en tiempo real de campos requeridos.

## 👥 Créditos y Autoría

- **Desarrollo y Programación**: Juan Diego Paredes Gámez
- **Diseño UI/UX**: Basado en requerimientos del Comité Organizador
- **Organización**: Universidad de Los Andes (ULA)
- **Sede Anfitriona**: Universidad Nacional Experimental del Táchira (UNET)

---

**Nota Técnica**: Este proyecto está optimizado para ejecutarse en servidores Linux/Windows con soporte para Node.js v16+ y MySQL 8.0.