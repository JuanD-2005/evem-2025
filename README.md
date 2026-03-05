# EVEM & DIM 2026 - Portal Web y Sistema de Gestión

Portal oficial y sistema de gestión integral para la **XXVIII Escuela Venezolana para la Enseñanza de la Matemática (EVEM)** y el **Día Internacional de las Matemáticas (DIM)**. Organizado por la Universidad de Los Andes (ULA) y hospedado en la Universidad Nacional Experimental del Táchira (UNET).

> **Estado:** 🟢 Listo para Producción / Full Stack Funcional
> **Edición:** 2026
> **Arquitectura:** Frontend (Vanilla JS) + Backend (PHP/MySQL)

## 🚀 Tecnologías Utilizadas

El proyecto ha sido refactorizado para garantizar compatibilidad total con la infraestructura de servidores web tradicionales (Apache/Nginx) de la institución.

### Frontend (Cliente)
- **HTML5 Semántico**: Estructura optimizada, modular y accesible.
- **CSS3 Moderno**: Diseño responsivo, variables CSS, Glassmorphism, animaciones y diseño institucional.
- **JavaScript (Vanilla ES6+)**: Lógica asíncrona (Fetch API), generación de PDFs en cliente y manipulación del DOM.
- **Librerías de Terceros**:
  - `SweetAlert2`: Notificaciones y alertas modernas.
  - `html2canvas` & `jsPDF`: Generación y exportación de certificados dinámicos al vuelo.

### Backend (Servidor)
- **PHP (Nativo - PDO)**: Procesamiento de rutas, lógica de negocio y seguridad.
- **MySQL**: Base de datos relacional para control de cupos y registros.

---

## 📁 Estructura del Proyecto

```text
evem-2026/
├── index.html                  # Página de Inicio (Hero, Quienes Somos, Accesos)
├── assets/
│   └── images/                 # Logos institucionales y recursos visuales
├── css/
│   ├── layout.css              # Estilos estructurales y Header Institucional
│   ├── components.css          # Botones animados y tarjetas
│   └── responsive.css          # Adaptabilidad a dispositivos móviles
├── js/
│   ├── api.js                  # Cliente API (Puente Frontend-Backend EVEM)
│   └── main.js                 # Lógica de formularios y UI
├── pages/
│   ├── contact.html            # Formulario de Inscripción EVEM (Asistente/Poster)
│   ├── courses.html            # Catálogo dinámico de cursos (EVEM)
│   ├── conferencias.html       # Catálogo VIP de Conferencias Magistrales
│   ├── alojamiento.html        # Información de hospedaje con carrusel JS
│   ├── dim.html                # Landing Page oficial del evento DIM
│   ├── dim-registro.html       # Formulario Split-Screen de inscripción al DIM
│   ├── dim-certificado.html    # Generador de certificados PDF para el DIM
│   └── admin-dim.html          # (Secreto) Panel de administración de pagos DIM
└── backend/                    
    └── api.php                 # Enrutador y controlador principal de la Base de Datos

```

## ⚙️ Instalación y Despliegue

Este proyecto no requiere compiladores (como Node/NPM). Está diseñado para ser arrastrado y ejecutado (Plug & Play) en cualquier servidor LAMP/WAMP.

### 1. Base de Datos (MySQL)

* Iniciar Apache y MySQL (ej. mediante XAMPP).
* Crear una base de datos llamada `evem`.
* Importar las consultas SQL para generar las siguientes tablas:
* `courses` (Catálogo de cursos y control de cupos).
* `participants` (Inscritos en EVEM).
* `dim_participants` (Inscritos en el evento DIM con control de pagos).



### 2. Configuración del Backend (PHP)

* Abrir el archivo `backend/api.php`.
* Actualizar las credenciales de la base de datos según el entorno (Local o Producción):

```php
$host = "localhost";
$db_name = "evem";       // Nombre de la base de datos
$username = "usuario";   // Credencial de BD
$password = "clave";     // Credencial de BD

```

### 3. Despliegue en Producción

* Subir el contenido completo de la carpeta raíz al directorio público del servidor (`public_html` o `htdocs`) mediante FTP/FileZilla.

---

## 🔌 API Endpoints (api.php)

El backend de PHP actúa como una API RESTful que responde a parámetros GET/POST:

### Módulo EVEM

| Método | Acción (`?action=`) | Descripción |
| --- | --- | --- |
| GET | `courses` | Obtiene la lista de cursos activos y cupos disponibles. |
| POST | `register` | Registra un usuario en EVEM y descuenta cupos del curso. |

### Módulo DIM

| Método | Acción (`?action=`) | Descripción |
| --- | --- | --- |
| POST | `register_dim` | Registra un usuario en el evento DIM. |
| GET | `check_certificate` | Valida si una cédula existe y si su pago fue aprobado. |
| GET | `get_dim_participants` | (Admin) Lista todos los inscritos en el DIM. |
| POST | `toggle_payment` | (Admin) Cambia el estado de pago de un usuario del DIM. |

---

## ✨ Características Destacadas (v3.0)

### 1. Ecosistema Multievento

El portal ahora maneja de forma paralela y sin conflictos dos eventos masivos (EVEM y DIM), con diseños visuales distintos pero unificados bajo una misma arquitectura.

### 2. Módulo DIM Completo

* **Landing Exclusiva:** Diseño basado en tonalidades púrpura con navegación propia.
* **Registro de Alta Conversión:** Formulario *Split-Layout* con panel informativo en cristal esmerilado.
* **Generador de Certificados:** Sistema *Client-Side* que verifica el estatus de pago y dibuja en pantalla un diploma en PDF listo para descargar, incorporando candados de fecha para evitar descargas previas a la finalización del evento.
* **Panel Administrativo:** Interfaz secreta para la validación y gestión de pagos en tiempo real.

### 3. Mejoras UI/UX

* Interfaces adaptativas (100% Mobile Friendly) con correcciones de superposición en el Header.
* Catálogo de conferencias magistrales con diseño VIP alternado.
* Carrusel de imágenes sin dependencias externas (Vanilla JS) para la sede de alojamiento.

---

## 👥 Créditos y Autoría

* **Desarrollo Full Stack**: Juan Paredes
* **Diseño UI/UX**: Basado en requerimientos del Comité Organizador
* **Organización**: Universidad de Los Andes (ULA)
* **Sede Anfitriona**: Universidad Nacional Experimental del Táchira (UNET)

```

```