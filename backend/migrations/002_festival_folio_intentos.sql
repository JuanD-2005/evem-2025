-- Control de intentos por IP para la verificación pública de folios.
-- Son datos de estudiantes de colegio en una página sin login: sin esto,
-- cualquiera puede recorrer el espacio de folios a fuerza bruta hasta
-- encontrar uno válido (el alfabeto es grande, pero igual no hay por qué
-- dejar la puerta abierta a intentarlo sin límite).
--
-- Se guarda un renglón por intento (no un contador acumulado) para poder
-- contar por ventana de tiempo con un simple COUNT + WHERE; se borran los
-- renglones viejos en cada request para que la tabla no crezca sin límite.

CREATE TABLE IF NOT EXISTS festival_folio_intentos (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  ip         VARCHAR(45) NOT NULL,
  creado_en  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ip_fecha (ip, creado_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
