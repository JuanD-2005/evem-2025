-- Tabla de folios emitidos para los certificados del Festival.
-- Vive en la misma base MySQL/MariaDB que festival_teams (evem_2025),
-- no es un archivo aparte. No hay runner de migraciones en este
-- proyecto (ver README: los cambios de esquema se aplican a mano con
-- un cliente SQL) — este archivo es el registro en git de qué se
-- corrió, para que el historial cuente la historia aunque no se
-- ejecute automáticamente.
--
-- Estructura (id + UNIQUE KEY en folio) tomada de la revisión de
-- Gemini para alinear tipos/collation con el resto del proyecto.
-- ON DUPLICATE KEY UPDATE dispara con cualquier índice único, no
-- solo con PRIMARY KEY, así que la idempotencia del endpoint de
-- sincronización funciona igual con folio como UNIQUE que como PK.
--
-- No se guarda cédula. La página pública de verificación solo puede
-- filtrar/mostrar lo que esta tabla contiene, así que la ausencia de
-- cédula acá es la garantía real de que nunca se expone, no una
-- promesa de la capa de presentación.
--
-- fecha es VARCHAR, NO TIMESTAMP: festivalController.php::
-- sync_festival_certificados manda ahí el texto libre del campo
-- "Fecha del evento" del generador ("28 y 29 de mayo de 2026", puede
-- cubrir un rango) tal cual como string. Una columna TIMESTAMP
-- rechaza ese valor: la primera revisión de esta migración la traía
-- como TIMESTAMP y habría hecho fallar cada fila de cada sync real.

CREATE TABLE IF NOT EXISTS `festival_certificados` (
  `id`       int(11) NOT NULL AUTO_INCREMENT,
  `folio`    varchar(50)  COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre`   varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `proyecto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo`     varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha`    varchar(60)  COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_folio_unico` (`folio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
