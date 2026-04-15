-- ============================================================
-- BASE DE DATOS: vocacional_uets
-- Sistema OrientaTech - UETS 2025-2026
-- ============================================================
-- CONTRASEÑAS: guardadas en texto plano.
--   "Admin123" → e64b78fc3bc91bcbc7dc232ba8ec59e0
-- ============================================================

CREATE DATABASE IF NOT EXISTS vocacional_uets CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vocacional_uets;

-- ── TABLA: usuarios ─────────────────────────────────────────
-- Incluye 3 columnas para las respuestas de seguridad
-- que se usan para recuperar la contraseña sin correo
CREATE TABLE IF NOT EXISTS usuarios (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    nombre           VARCHAR(100) NOT NULL,
    apellido         VARCHAR(100) NOT NULL,
    correo           VARCHAR(150) NOT NULL UNIQUE,
    contrasena       VARCHAR(255) NOT NULL,
    rol              ENUM('estudiante','dece','rector','admin','coordinador') NOT NULL DEFAULT 'estudiante',
    curso            VARCHAR(10)  DEFAULT NULL,
    -- Preguntas de seguridad (para recuperar contraseña)
    especialidad_coord VARCHAR(100) DEFAULT NULL, -- Especialidad del coordinador
    seg_resp1        VARCHAR(200) DEFAULT NULL,  -- Comida favorita
    seg_resp2        VARCHAR(200) DEFAULT NULL,  -- Nombre de su mascota
    seg_resp3        VARCHAR(200) DEFAULT NULL,  -- Ciudad donde nació
    activo           TINYINT(1)   NOT NULL DEFAULT 1,
    fecha_creacion   DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── TABLA: respuestas_encuesta ───────────────────────────────
CREATE TABLE IF NOT EXISTS respuestas_encuesta (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario      INT NOT NULL,
    numero_pregunta INT NOT NULL,
    respuesta       VARCHAR(50) NOT NULL,
    texto_pregunta  TEXT,
    texto_respuesta TEXT,
    fecha_respuesta DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── TABLA: resultados ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS resultados (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario      INT NOT NULL,
    especialidad    VARCHAR(150) NOT NULL,
    justificacion   TEXT NOT NULL,
    fecha_resultado DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── TABLA: mensajes_contacto ─────────────────────────────────
CREATE TABLE IF NOT EXISTS mensajes_contacto (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario  INT NOT NULL,
    asunto      VARCHAR(200) NOT NULL,
    mensaje     TEXT NOT NULL,
    leido       TINYINT(1) NOT NULL DEFAULT 0,
    respuesta   TEXT DEFAULT NULL,
    fecha_respuesta DATETIME DEFAULT NULL,
    fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── USUARIOS DE PRUEBA ───────────────────────────────────────
-- Contraseña de todos: Admin123
-- Respuestas de seguridad de prueba (en minúsculas siempre)
INSERT INTO usuarios (nombre, apellido, correo, contrasena, rol, curso, especialidad_coord, seg_resp1, seg_resp2, seg_resp3) VALUES
('Super',       'Admin',    'admin@uets.edu.ec',         'Admin123', 'admin',        NULL,  NULL,          'pizza',  'rex',   'cuenca'),
('Admin',       'Rector',   'rector@uets.edu.ec',        'Admin123', 'rector',       NULL,  NULL,          'pizza',  'rex',   'cuenca'),
('Ana',         'Guzmán',   'dece@uets.edu.ec',          'Admin123', 'dece',         NULL,  NULL,          'sushi',  'luna',  'quito'),
('Carlos',      'Pérez',    'carlos@uets.edu.ec',        'Admin123', 'estudiante',   '10A', NULL,          'arroz',  'toby',  'guayaquil'),
('María',       'Torres',   'maria@uets.edu.ec',         'Admin123', 'estudiante',   '10B', NULL,          'pizza',  'mochi', 'cuenca'),
('Coord',       'IEME',     'coord.ieme@uets.edu.ec',    'Admin123', 'coordinador',  NULL,  'IEME',        'pan',    'rex',   'cuenca'),
('Coord',       'MCM',      'coord.mcm@uets.edu.ec',     'Admin123', 'coordinador',  NULL,  'MCM',         'arroz',  'max',   'quito'),
('Coord',       'EMA',      'coord.ema@uets.edu.ec',     'Admin123', 'coordinador',  NULL,  'EMA',         'sopa',   'luna',  'loja'),
('Coord',       'Mecatronica','coord.mecatronica@uets.edu.ec','Admin123','coordinador',NULL, 'Mecatrónica', 'pollo',  'rex',   'cuenca'),
('Coord',       'Informatica','coord.informatica@uets.edu.ec','Admin123','coordinador',NULL, 'Informática', 'pizza',  'toby',  'ambato'),
('Coord',       'Ciencias',  'coord.ciencias@uets.edu.ec','Admin123', 'coordinador',  NULL,  'Ciencias',   'arroz',  'mia',   'guayaquil');
