// ============================================================
// EVEM 2025 - BACKEND API (server.js) - VERSIÓN COMPLETA
// ============================================================

const express = require('express');
const mysql = require('mysql2/promise');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const cors = require('cors');
const helmet = require('helmet');
const rateLimit = require('express-rate-limit');
const { body, validationResult } = require('express-validator');
require('dotenv').config();

const app = express();

// --- CONFIGURACIÓN ---
app.use(helmet());
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Conexión a Base de Datos
const pool = mysql.createPool({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_NAME,
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
});

// --- RUTAS ---

// 1. Obtener Cursos (GET)
app.get('/api/courses', async (req, res) => {
    try {
        const [courses] = await pool.query(
            'SELECT * FROM courses WHERE is_active = TRUE'
        );
        res.json(courses);
    } catch (error) {
        console.error(error);
        res.status(500).json({ error: 'Error al obtener cursos' });
    }
});

// 2. Registrar Participante (POST) - ACTUALIZADO PARA POSTERS
app.post('/api/register', [
    body('cedula').trim().notEmpty(),
    body('email').isEmail(),
    body('coursePreference').notEmpty(),
    body('participationType').isIn(['participante', 'poster'])
], async (req, res) => {
    const errors = validationResult(req);
    if (!errors.isEmpty()) return res.status(400).json({ errors: errors.array() });

    // Recibimos los nuevos datos
    const { 
        cedula, fullName, email, phone, 
        institution, state, city, position, experienceYears, 
        coursePreference, participationType, posterTitle, posterAbstract, // Nuevos campos
        previousParticipation, wantsNewsletter, acceptedTerms 
    } = req.body;

    try {
        const [existing] = await pool.query('SELECT id FROM participants WHERE cedula = ?', [cedula]);
        if (existing.length > 0) {
            return res.status(409).json({ error: 'Esta cédula ya está inscrita.' });
        }

        const [course] = await pool.query('SELECT * FROM courses WHERE title = ?', [coursePreference]);
        if (course.length === 0) return res.status(404).json({ error: 'Curso no encontrado' });
        
        const isPrevious = previousParticipation === 'Si'; 

        // INSERT ACTUALIZADO
        const [result] = await pool.query(
            `INSERT INTO participants (
                cedula, full_name, email, phone, institution, 
                state, city, position, experience_years, 
                course_preference, participation_type, poster_title, poster_abstract,
                previous_participation, wants_newsletter, accepted_terms
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
            [
                cedula, fullName, email, phone, institution,
                state, city, position, experienceYears,
                coursePreference, participationType, posterTitle, posterAbstract,
                isPrevious, wantsNewsletter, acceptedTerms
            ]
        );

        await pool.query('UPDATE courses SET current_enrollment = current_enrollment + 1 WHERE title = ?', [coursePreference]);

        res.status(201).json({ message: 'Inscrito exitosamente', id: result.insertId });
    } catch (error) {
        console.error("Error detallado:", error);
        res.status(500).json({ error: 'Error al registrar en la base de datos' });
    }
});

// --- INICIAR SERVIDOR ---
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`🚀 Servidor EVEM corriendo en http://localhost:${PORT}`);
});