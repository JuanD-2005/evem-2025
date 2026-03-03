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
$db_name = "evem";      
$username = "evem";     // El usuario de la UNET
$password = "BD.Evem*2026"; // La super clave

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

// --- RUTA: REGISTRARSE ---
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
else {
    echo json_encode(["status" => "API PHP Activa"]);
}
?>