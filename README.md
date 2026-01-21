# EVEM 2025 - Portal Web

Portal oficial de la XXVII Escuela Venezolana para la Enseñanza de la Matemática, organizada por la Universidad de Los Andes (ULA) y hospedada en la Universidad Nacional Experimental del Táchira (UNET).

## 🚀 Características

- **Diseño Responsive**: Adaptado para desktop, tablet y móvil
- **Arquitectura Modular**: Código organizado y mantenible
- **Sin dependencias externas**: Solo HTML, CSS y JavaScript vanilla
- **Optimizado para rendimiento**: Lazy loading, animaciones suaves
- **Accesible**: Semántica HTML5, navegación por teclado
- **SEO-friendly**: Meta tags, estructura semántica

## 📁 Estructura del Proyecto

```
evem-2025/
├── index.html                 # Página principal
├── css/
│   ├── reset.css             # Normalización de estilos
│   ├── variables.css         # Variables CSS (colores, espaciados)
│   ├── typography.css        # Estilos tipográficos
│   ├── layout.css            # Layouts y estructura
│   ├── components.css        # Componentes reutilizables
│   └── responsive.css        # Media queries
├── js/
│   ├── main.js               # Inicialización principal
│   ├── navigation.js         # Navegación y menú
│   ├── animations.js         # Animaciones
│   └── data.js               # Datos del evento
├── pages/
│   ├── courses.html          # Página de cursos
│   ├── tribute.html          # Homenaje a Darío Durán
│   └── contact.html          # Contacto e inscripción
└── assets/
    └── images/               # Imágenes del sitio
```

## 🎨 Paleta de Colores

- **Primary**: #4267B2 (Azul EVEM)
- **Primary Dark**: #2D4A8C
- **Secondary**: #F4A261 (Naranja)
- **Accent**: #E76F51
- **Backgrounds**: #FFFFFF, #F8F9FA, #2C3E50

## 📋 Páginas Incluidas

### 1. index.html - Página Principal
- Hero section con imagen de fondo
- Sección "Sobre Nosotros"
- Estadísticas del evento
- Cards informativas
- Call to Action
- Footer completo

### 2. pages/courses.html - Cursos
- Listado detallado de los 5 cursos
- Información de profesores
- Niveles y duración
- Sección de información práctica

### 3. pages/tribute.html - Homenaje
- Historia de Darío Durán
- Mensaje de Arístides
- Legado de EVEM
- Diseño emotivo y respetuoso

### 4. pages/contact.html - Contacto
- Formulario de inscripción completo
- Información de contacto
- Ubicación
- Validación de formularios

## 🛠️ Funcionalidades JavaScript

### main.js
- Inicialización de la app
- Sistema de notificaciones
- Manejo de formularios
- Modo debug (Ctrl+Shift+D)
- Detección de conexión

### navigation.js
- Menú móvil responsive
- Scroll suave
- Enlaces activos según scroll
- Navbar sticky con sombra

### animations.js
- Animaciones al scroll (Intersection Observer)
- Botón "Volver arriba"
- Efectos hover en cards
- Contadores animados
- Lazy loading de imágenes

### data.js
- Datos centralizados del evento
- Cursos y profesores
- Información de contacto
- Fácil actualización

## 🚀 Instalación y Uso

### Opción 1: Servidor Local Simple
```bash
# Con Python 3
python -m http.server 8000

# Con Node.js y http-server
npx http-server -p 8000
```

### Opción 2: Live Server (VS Code)
1. Instala la extensión "Live Server"
2. Click derecho en index.html
3. Selecciona "Open with Live Server"

### Opción 3: Abrir directamente
Simplemente abre `index.html` en tu navegador

## 📝 Personalización

### Colores
Edita `css/variables.css` para cambiar la paleta de colores:
```css
:root {
    --color-primary: #4267B2;
    --color-secondary: #F4A261;
    /* ... más variables */
}
```

### Contenido
Edita `js/data.js` para actualizar información del evento:
```javascript
const EVEM_DATA = {
    event: {
        name: "...",
        year: 2025,
        dates: { ... },
        location: { ... }
    },
    courses: [ ... ]
};
```

### Imágenes
Reemplaza las imágenes en `assets/images/` manteniendo los nombres:
- `hero-tachira.jpg` - Imagen principal del hero
- `evem-logo.png` - Logo de EVEM
- `unet-logo.png` - Logo UNET
- `unet-campus.jpg`, `cursos.jpg`, etc.

## 🔧 Características Técnicas

### CSS
- Variables CSS para fácil personalización
- Flexbox y CSS Grid para layouts
- Transitions y animations suaves
- Mobile-first approach
- Print styles incluidos

### JavaScript
- ES6+ features
- Clases y módulos
- Async/await
- Intersection Observer API
- Event delegation
- LocalStorage (si se necesita persistencia)

### Accesibilidad
- Semántica HTML5
- ARIA labels
- Navegación por teclado
- Contraste de colores WCAG AA
- Focus visible

## 📱 Responsive Breakpoints

- **Desktop**: > 1024px
- **Tablet**: 768px - 1024px
- **Mobile**: < 768px
- **Small Mobile**: < 480px

## 🐛 Debug Mode

Activa el modo debug presionando `Ctrl+Shift+D`:
- Muestra outlines en todos los elementos
- Log de datos en consola
- Útil para desarrollo

## 📞 Soporte y Contacto

Para dudas o modificaciones:
- Email: evem.tachira@gmail.com
- Tel: +58 (276) 353-0422

## 📄 Licencia

Este proyecto es para uso de EVEM 2025. Todos los derechos reservados a la Universidad de Los Andes (ULA) y Universidad Nacional Experimental del Táchira (UNET).

## 🚀 Próximas Mejoras Sugeridas

1. **Backend Integration**
   - Conectar formulario a base de datos
   - Sistema de autenticación
   - Panel de administración

2. **Funcionalidades Avanzadas**
   - Galería de fotos de eventos anteriores
   - Sistema de pagos online
   - Chat en vivo
   - Calendario interactivo

3. **Optimizaciones**
   - Service Workers para PWA
   - Compresión de imágenes
   - Minificación de CSS/JS
   - CDN para assets estáticos

4. **Integración de Mapas**
   - Google Maps embebido
   - Indicaciones de cómo llegar
   - Puntos de interés cercanos

5. **Multiidioma**
   - Soporte para inglés
   - Sistema i18n

## 👥 Créditos

- **Diseño**: Basado en el diseño original de Figma
- **Desarrollo**: Juan Diego Paredes Gámez
- **Coordinación EVEM**: Universidad de Los Andes
- **Sede 2025**: UNET

---

**Última actualización**: Enero 2025
**Versión**: 1.0.0