<?php
// backend/controllers/festivalController.php

function register_festival($conn) {
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->teamName) && !empty($data->institution) && !empty($data->members)) {
        try {
            // Asumimos que existe una tabla llamada festival_teams
            $sql = "INSERT INTO festival_teams (team_name, institution, area, members) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $data->teamName,
                $data->institution,
                $data->area ?? '',
                $data->members
            ]);

            http_response_code(201);
            echo json_encode(["message" => "Equipo registrado exitosamente en el Festival."]);
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error en la BD: " . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Por favor completa los campos obligatorios."]);
    }
}

function get_festival_teams($conn) {
    require_admin_session('festival');
    try {
        $stmt = $conn->query("SELECT * FROM festival_teams ORDER BY created_at DESC");
        http_response_code(200);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error interno: " . $e->getMessage()]);
    }
}

function toggle_festival_status($conn) {
    require_admin_session('festival');
    $data = json_decode(file_get_contents("php://input"));
    if (isset($data->id) && isset($data->status)) {
        try {
            $stmt = $conn->prepare("UPDATE festival_teams SET status = ? WHERE id = ?");
            $stmt->execute([$data->status, $data->id]);
            http_response_code(200);
            echo json_encode(["success" => true]);
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar: " . $e->getMessage()]);
        }
    }
}

// Sincroniza el lote de certificados generado en el navegador (folio ya
// asignado ahí con crypto.getRandomValues) hacia festival_certificados.
// Protegido con sesión de admin: quien genera y sincronice el lote real
// tiene que haber iniciado sesión en el panel del Festival primero.
//
// Idempotente por diseño: folio es PRIMARY KEY, así que el UPSERT
// (ON DUPLICATE KEY UPDATE) hace que reintentar el mismo folio
// sobrescriba la misma fila en vez de fallar o duplicar. Sincronizar
// el mismo lote dos veces por una conexión cortada a mitad de camino
// es seguro.
function sync_festival_certificados($conn) {
    require_admin_session('festival');

    $data = json_decode(file_get_contents("php://input"));

    if (!is_array($data) || empty($data)) {
        http_response_code(400);
        echo json_encode(["error" => "Se esperaba un array de certificados no vacío."]);
        exit();
    }

    $tiposValidos = ['participacion', 'ponente', 'expositor', 'ganador', 'organizador', 'asistente'];

    $sql = "INSERT INTO festival_certificados (folio, nombre, proyecto, tipo, fecha)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                nombre = VALUES(nombre),
                proyecto = VALUES(proyecto),
                tipo = VALUES(tipo),
                fecha = VALUES(fecha)";

    try {
        $stmt = $conn->prepare($sql);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error interno: " . $e->getMessage()]);
        exit();
    }

    $guardados = 0;
    $errores = [];

    foreach ($data as $i => $item) {
        $folio    = isset($item->folio)    ? trim((string) $item->folio)    : '';
        $nombre   = isset($item->nombre)   ? trim((string) $item->nombre)   : '';
        $proyecto = (!empty($item->proyecto)) ? trim((string) $item->proyecto) : null;
        $tipo     = isset($item->tipo)     ? strtolower(trim((string) $item->tipo)) : '';
        $fecha    = isset($item->fecha)    ? trim((string) $item->fecha)    : '';

        if ($folio === '' || $nombre === '' || $fecha === '') {
            $errores[] = ["index" => $i, "folio" => $folio, "error" => "Falta folio, nombre o fecha."];
            continue;
        }
        if (!in_array($tipo, $tiposValidos, true)) {
            $tipo = 'participacion';
        }

        try {
            $stmt->execute([$folio, $nombre, $proyecto, $tipo, $fecha]);
            $guardados++;
        } catch (Exception $e) {
            $errores[] = ["index" => $i, "folio" => $folio, "error" => "No se pudo guardar."];
        }
    }

    http_response_code(200);
    echo json_encode([
        "success"   => true,
        "guardados" => $guardados,
        "total"     => count($data),
        "errores"   => $errores,
    ]);
}

// Límite: 20 intentos por IP cada 5 minutos. "Básico" a propósito — el
// objetivo es frenar un barrido automatizado del espacio de folios, no
// molestar a alguien que se equivoca tipeando un par de veces.
const FOLIO_RATE_LIMIT_MAX = 20;
const FOLIO_RATE_LIMIT_MINUTOS = 5;

function verify_festival_certificado($conn) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';

    try {
        // Housekeeping: se borran los intentos fuera de la ventana en
        // cada request en vez de tener un cron aparte para esto.
        $conn->prepare("DELETE FROM festival_folio_intentos WHERE creado_en < (NOW() - INTERVAL ? MINUTE)")
             ->execute([FOLIO_RATE_LIMIT_MINUTOS]);

        $stmt = $conn->prepare("SELECT COUNT(*) FROM festival_folio_intentos WHERE ip = ? AND creado_en >= (NOW() - INTERVAL ? MINUTE)");
        $stmt->execute([$ip, FOLIO_RATE_LIMIT_MINUTOS]);
        $intentos = (int) $stmt->fetchColumn();

        if ($intentos >= FOLIO_RATE_LIMIT_MAX) {
            http_response_code(429);
            echo json_encode(["error" => "Demasiados intentos. Esperá unos minutos y volvé a intentar."]);
            exit();
        }

        $conn->prepare("INSERT INTO festival_folio_intentos (ip) VALUES (?)")->execute([$ip]);
    } catch (Exception $e) {
        // Si el limitador falla, seguimos: es una protección extra, no
        // el motivo de ser del endpoint. No tumbamos la verificación
        // por esto.
    }

    $folio = isset($_GET['folio']) ? trim($_GET['folio']) : '';
    if ($folio === '') {
        http_response_code(400);
        echo json_encode(["error" => "Falta el folio a verificar."]);
        exit();
    }

    try {
        // SELECT explícito de solo nombre y proyecto: la tabla no tiene
        // cédula, pero además nunca pedimos tipo ni fecha acá — lo que
        // esta consulta no trae, esta página no lo puede mostrar.
        $stmt = $conn->prepare("SELECT nombre, proyecto FROM festival_certificados WHERE UPPER(folio) = UPPER(?)");
        $stmt->execute([$folio]);
        $cert = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cert) {
            http_response_code(200);
            echo json_encode(["success" => true, "nombre" => $cert['nombre'], "proyecto" => $cert['proyecto']]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Folio no encontrado. Verificá que esté bien escrito."]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error interno al verificar."]);
    }
}

function check_festival_certificate($conn) {
    $team_name = isset($_GET['team']) ? trim($_GET['team']) : '';
    try {
        // Solo es valido si el estatus es 1 (Pagado/Aprobado)
        $stmt = $conn->prepare("SELECT * FROM festival_teams WHERE team_name = ? AND status = 1");
        $stmt->execute([$team_name]);
        $team = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($team) {
            http_response_code(200);
            echo json_encode(["success" => true, "team" => $team]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Certificado invalido o el equipo no esta solvente."]);
        }
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error interno: " . $e->getMessage()]);
    }
}
