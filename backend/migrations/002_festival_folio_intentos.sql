-- Control de intentos por IP para la verificación pública de folios.
-- Son datos de estudiantes de colegio en una página sin login: sin esto,
-- cualquiera puede recorrer el espacio de folios a fuerza bruta hasta
-- encontrar uno válido.
--
-- Diseño: un renglón POR INTENTO (no un contador acumulado por IP), con
-- índice compuesto (ip, creado_en) para contar por ventana de tiempo con
-- un simple COUNT + WHERE. verify_festival_certificado() en
-- festivalController.php borra los renglones vencidos en cada request
-- (housekeeping sin cron aparte) y cuenta los intentos de esa IP dentro
-- de los últimos 5 minutos antes de dejar pasar la búsqueda.
--
-- A propósito NO es "una fila por IP con un contador que se
-- incrementa": ese diseño (id + ip UNIQUE + intentos + last_attempt)
-- se probó en una revisión anterior de esta migración y bloqueaba a
-- una IP para siempre una vez que llegaba al tope, sin ventana de
-- reseteo — problemático con IPs compartidas (el wifi de un colegio
-- con varios estudiantes buscando su folio). Con fila-por-intento, el
-- límite se resetea solo: en cuanto los intentos viejos salen de la
-- ventana de 5 minutos, dejan de contar.

CREATE TABLE IF NOT EXISTS `festival_folio_intentos` (
  `id`        int(11) NOT NULL AUTO_INCREMENT,
  `ip`        varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `creado_en` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ip_fecha` (`ip`, `creado_en`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
