-- Tabla de folios emitidos para los certificados del Festival.
-- Vive en la misma base MySQL/MariaDB que festival_teams (evem_2025),
-- no es un archivo aparte. No hay runner de migraciones en este
-- proyecto (ver README: los cambios de esquema se aplican a mano con
-- un cliente SQL) — este archivo es el registro en git de qué se
-- corrió, para que el historial cuente la historia aunque no se
-- ejecute automáticamente.
--
-- folio es PRIMARY KEY a propósito: es lo que hace idempotente al
-- endpoint de sincronización (INSERT ... ON DUPLICATE KEY UPDATE),
-- reintentar con el mismo folio nunca crea una fila nueva.
--
-- No se guarda cédula. La página pública de verificación solo puede
-- filtrar/mostrar lo que esta tabla contiene, así que la ausencia de
-- cédula acá es la garantía real de que nunca se expone, no una
-- promesa de la capa de presentación.
--
-- fecha queda como texto (no DATE): el campo "Fecha del evento" del
-- generador es descriptivo y libre ("28 y 29 de mayo de 2026", puede
-- cubrir un rango), no una fecha ISO única — forzarlo a DATE hubiera
-- exigido inventar un campo que el formulario no tiene.

CREATE TABLE IF NOT EXISTS festival_certificados (
  folio      VARCHAR(40)  NOT NULL PRIMARY KEY,
  nombre     VARCHAR(255) NOT NULL,
  proyecto   VARCHAR(255) NULL,
  tipo       VARCHAR(30)  NOT NULL,
  fecha      VARCHAR(60)  NOT NULL,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
