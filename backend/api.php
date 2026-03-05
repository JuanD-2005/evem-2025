<?php
// api.php - Backend Oficial EVEM 2026 (PHP Nativo)

// 1. Configuración de Cabeceras (Permitir conexiones)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Manejo de petición preliminar (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Configuración de Base de Datos
$host = "localhost";
$db_name = "evem_2025"; // Ojo: Cámbialo a "evem" cuando lo subas a la UNET
$username = "root";     // Ojo: Cámbialo al usuario de la UNET
$password = "";


try {
    $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name . ";charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión: " . $exception->getMessage()]);
    exit();
}

// 3. Lógica de Rutas
$action = isset($_GET['action']) ? $_GET['action'] : '';

// --- RUTA: OBTENER CURSOS ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'courses') {
    try {
        $stmt = $conn->prepare("SELECT * FROM courses WHERE is_active = TRUE");
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al cargar cursos"]);
    }
}

// --- RUTA: REGISTRARSE (EVEM GENERAL) ---
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'register') {
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->cedula) || empty($data->email)) {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos"]);
        exit();
    }

    try {
        // Verificar cédula duplicada
        $check = $conn->prepare("SELECT id FROM participants WHERE cedula = ?");
        $check->execute([$data->cedula]);
        if ($check->rowCount() > 0) {
            http_response_code(409); // Conflicto
            echo json_encode(["error" => "Esta cédula ya está inscrita."]);
            exit();
        }

        // Verificar Cupos
        $courseCheck = $conn->prepare("SELECT max_capacity, current_enrollment FROM courses WHERE title = ?");
        $courseCheck->execute([$data->coursePreference]);
        $course = $courseCheck->fetch(PDO::FETCH_ASSOC);

        if (!$course) {
            throw new Exception("Curso no encontrado");
        }

        // Insertar Participante
        $sql = "INSERT INTO participants (
                    cedula, full_name, email, phone, institution, 
                    state, city, position, experience_years, 
                    course_preference, participation_type, poster_title, poster_abstract,
                    previous_participation, wants_newsletter, accepted_terms
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $data->cedula,
            $data->fullName,
            $data->email,
            $data->phone,
            $data->institution,
            $data->state,
            $data->city,
            $data->position,
            $data->experienceYears,
            $data->coursePreference,
            $data->participationType,
            $data->posterTitle ?? null,    // Si no existe, pone NULL
            $data->posterAbstract ?? null, // Si no existe, pone NULL
            $data->previousParticipation === 'Si' ? 1 : 0,
            $data->wantsNewsletter ? 1 : 0,
            $data->acceptedTerms ? 1 : 0
        ]);

        $newId = $conn->lastInsertId();

        // Actualizar Cupos
        $update = $conn->prepare("UPDATE courses SET current_enrollment = current_enrollment + 1 WHERE title = ?");
        $update->execute([$data->coursePreference]);

        http_response_code(201);
        echo json_encode(["message" => "Inscrito exitosamente", "id" => $newId]);

    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error interno: " . $e->getMessage()]);
    }
}

// --- NUEVA RUTA: REGISTRARSE AL EVENTO DIM ---
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'register_dim') {
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->cedula) || empty($data->fullName) || empty($data->email)) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos obligatorios"]);
        exit();
    }

    try {
        // Verificar si la cédula ya se anotó en el DIM
        $check = $conn->prepare("SELECT id FROM dim_participants WHERE cedula = ?");
        $check->execute([$data->cedula]);
        if ($check->rowCount() > 0) {
            http_response_code(409); // Conflicto
            echo json_encode(["error" => "Esta cédula ya está registrada en el evento DIM."]);
            exit();
        }

        // Insertar Participante en la tabla del DIM (has_paid queda en FALSE por defecto de la BD)
        $sql = "INSERT INTO dim_participants (cedula, full_name, email, phone, institution, state, city) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $data->cedula, 
            $data->fullName, 
            $data->email, 
            $data->phone, 
            $data->institution, 
            $data->state, 
            $data->city
        ]);

        http_response_code(201);
        echo json_encode(["message" => "Pre-inscripción al DIM exitosa"]);

    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error interno en el servidor: " . $e->getMessage()]);
    }
}

// --- NUEVA RUTA: VERIFICAR Y GENERAR CERTIFICADO DIM ---
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'check_certificate') {
    $cedula = isset($_GET['cedula']) ? $_GET['cedula'] : '';

    if (empty($cedula)) {
        http_response_code(400);
        echo json_encode(["error" => "Debes ingresar una cédula."]);
        exit();
    }

    try {
        // Buscamos a la persona
        $stmt = $conn->prepare("SELECT full_name, has_paid FROM dim_participants WHERE cedula = ?");
        $stmt->execute([$cedula]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            http_response_code(404);
            echo json_encode(["error" => "No se encontró esta cédula en los registros del DIM."]);
            exit();
        }

        // Verificamos si ya pagó (has_paid = 1)
        if ($user['has_paid'] == 0) {
            http_response_code(403);
            echo json_encode(["error" => "Inscripción registrada, pero el pago aún no ha sido verificado en el sistema. Contacta a administración."]);
            exit();
        }

        // Si existe y pagó, le devolvemos su nombre para pintar el diploma
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "fullName" => $user['full_name']
        ]);

    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error interno en el servidor: " . $e->getMessage()]);
    }
}

// --- RUTA (ADMIN): OBTENER LISTA DEL DIM ---
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get_dim_participants') {
    try {
        $stmt = $conn->query("SELECT id, cedula, full_name, email, institution, has_paid, DATE_FORMAT(registration_date, '%d/%m/%Y') as fecha FROM dim_participants ORDER BY registration_date DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al obtener lista"]);
    }
}

// --- RUTA (ADMIN): CAMBIAR ESTADO DE PAGO DIM ---
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'toggle_payment') {
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->id) || !isset($data->has_paid)) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos"]);
        exit();
    }

    try {
        $stmt = $conn->prepare("UPDATE dim_participants SET has_paid = ? WHERE id = ?");
        $stmt->execute([$data->has_paid, $data->id]);
        echo json_encode(["success" => true]);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al actualizar"]);
    }
}

// --- RESPUESTA POR DEFECTO (Si no envían action) ---
else {
    echo json_encode(["status" => "API PHP Activa"]);
}
?>