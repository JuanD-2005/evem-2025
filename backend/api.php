<?php
// api.php - Backend Oficial EVEM & DIM 2026 (PHP Nativo)

// 1. Configuración de Cabeceras (Permitir conexiones)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// ¡NUEVO!: Apagar la salida de advertencias HTML para no romper el JSON del frontend
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Manejo de petición preliminar (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Configuración de Base de Datos
$host = "localhost";
$db_name = "evem_2025"; // Ojo: Cámbialo a "evem" en la UNET
$username = "root";     // Ojo: Cámbialo al usuario de la UNET
$password = "";         // Ojo: Cámbialo a "BD.Evem*2026" en la UNET

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

// ==========================================
// RUTAS DE EVEM (Cursos y Registro General)
// ==========================================

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

// --- RUTA: REGISTRARSE (EVEM) ---
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'register') {
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->cedula) || empty($data->email)) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos obligatorios (Cédula o Email)"]);
        exit();
    }

    try {
        // 1. Verificar cédula duplicada
        $check = $conn->prepare("SELECT id FROM participants WHERE cedula = ?");
        $check->execute([$data->cedula]);
        if ($check->rowCount() > 0) {
            http_response_code(409); // Conflicto
            echo json_encode(["error" => "Esta cédula ya está inscrita en EVEM."]);
            exit();
        }

        // 2. Capturar el curso (Aceptamos que el JS lo llame coursePreference o course)
        $curso_recibido = $data->coursePreference ?? $data->course ?? '';

        // 3. Buscar el curso inteligentemente (Por Nombre O por ID)
        $courseCheck = $conn->prepare("SELECT title, max_capacity, current_enrollment FROM courses WHERE title = ? OR id = ?");
        $courseCheck->execute([$curso_recibido, $curso_recibido]);
        $course = $courseCheck->fetch(PDO::FETCH_ASSOC);

        if (!$course) {
            http_response_code(404);
            // Si falla, ahora te dirá EXACTAMENTE qué dato extraño está mandando el formulario
            echo json_encode(["error" => "Curso no encontrado. El sistema recibió el dato: '" . $curso_recibido . "'"]);
            exit();
        }

        // Si lo encontró, guardamos el nombre real y perfecto de la base de datos
        $titulo_curso_real = $course['title'];

        // 4. Insertar Participante con datos blindados
        $sql = "INSERT INTO participants (
                    cedula, full_name, email, phone, institution, 
                    state, city, position, experience_years, 
                    course_preference, participation_type, poster_title, poster_abstract,
                    previous_participation, wants_newsletter, accepted_terms
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $data->cedula ?? '',
            $data->fullName ?? '',
            $data->email ?? '',
            $data->phone ?? '',
            $data->institution ?? '',
            $data->state ?? '',
            $data->city ?? '',
            $data->position ?? '',
            $data->experienceYears ?? '',
            $titulo_curso_real, // Usamos el nombre real
            $data->participationType ?? 'Asistente',
            $data->posterTitle ?? null,
            $data->posterAbstract ?? null,
            (isset($data->previousParticipation) && $data->previousParticipation === 'Si') ? 1 : 0,
            !empty($data->wantsNewsletter) ? 1 : 0,
            !empty($data->acceptedTerms) ? 1 : 0
        ]);

        $newId = $conn->lastInsertId();

        // 5. Actualizar Cupos (Usando el nombre real)
        $update = $conn->prepare("UPDATE courses SET current_enrollment = current_enrollment + 1 WHERE title = ?");
        $update->execute([$titulo_curso_real]);

        http_response_code(201);
        echo json_encode(["message" => "Inscripción exitosa. ¡Te esperamos en la EVEM!"]);

    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error interno: " . $e->getMessage()]);
    }
}

// --- RUTA: REGISTRO FESTIVAL DE LAS CIENCIAS ---
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'register_festival') {
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
    exit();
}

// --- RUTA: VERIFICAR Y GENERAR CERTIFICADO EVEM ---
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'check_evem_certificate') {
    $cedula = isset($_GET['cedula']) ? $_GET['cedula'] : '';

    if (empty($cedula)) {
        http_response_code(400);
        echo json_encode(["error" => "Debes ingresar una cédula."]);
        exit();
    }

    try {
        $stmt = $conn->prepare("SELECT full_name, course_preference, has_paid FROM participants WHERE cedula = ?");
        $stmt->execute([$cedula]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            http_response_code(404);
            echo json_encode(["error" => "No se encontró esta cédula en los registros de EVEM."]);
            exit();
        }

        if (!isset($user['has_paid']) || $user['has_paid'] == 0) {
            http_response_code(403);
            echo json_encode(["error" => "El pago de inscripción a EVEM no ha sido verificado. Contacta a administración."]);
            exit();
        }

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "fullName" => $user['full_name'],
            "course" => $user['course_preference']
        ]);

    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error interno: " . $e->getMessage()]);
    }
}

// ==========================================
// RUTAS DEL EVENTO DIM (Registro y Certificados)
// ==========================================

// --- RUTA: REGISTRAR EQUIPO PARA LA TRIVIA DIM ---
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'register_dim_team') {
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->pseudonym) || empty($data->captainCedula)) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos obligatorios."]);
        exit();
    }

    try {
        // Verificar si el seudónimo ya fue elegido por otro equipo
        $check = $conn->prepare("SELECT id FROM dim_teams WHERE pseudonym = ?");
        $check->execute([$data->pseudonym]);
        if ($check->rowCount() > 0) {
            http_response_code(409); // Conflicto
            echo json_encode(["error" => "El seudónimo '" . $data->pseudonym . "' ya fue registrado por otro equipo. ¡Elige otro rápido!"]);
            exit();
        }

        // Insertar el nuevo equipo
        $sql = "INSERT INTO dim_teams (pseudonym, captain_cedula, captain_name, captain_phone, members) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $data->pseudonym,
            $data->captainCedula,
            $data->captainName,
            $data->captainPhone,
            $data->members
        ]);

        http_response_code(201);
        echo json_encode(["message" => "¡Equipo registrado exitosamente para la Trivia!"]);

    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error interno: " . $e->getMessage()]);
    }
}

// --- RUTA: BUSCAR EQUIPO PARA CERTIFICADOS DIM ---
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get_dim_team') {
    $pseudonym = isset($_GET['pseudonym']) ? trim($_GET['pseudonym']) : '';

    try {
        $stmt = $conn->prepare("SELECT * FROM dim_teams WHERE pseudonym = ?");
        $stmt->execute([$pseudonym]);
        $team = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($team) {
            http_response_code(200);
            echo json_encode($team);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No encontramos ningún equipo registrado con el seudónimo '" . $pseudonym . "'."]);
        }
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error interno: " . $e->getMessage()]);
    }
    exit();
}

// --- RUTA: OBTENER MURAL DE PARTICIPANTES PUBLICOS ---
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get_participants') {
    try {
        $sql = "SELECT full_name AS nombre, institution AS institucion, participation_type AS modalidad
                FROM participants
                ORDER BY id DESC";

        $stmt = $conn->query($sql);
        $participantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($participantes);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error interno al cargar el mural: " . $e->getMessage()]);
    }
    exit();
}

elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'register_dim') {
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->cedula) || empty($data->fullName) || empty($data->email)) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos obligatorios"]);
        exit();
    }

    try {
        $check = $conn->prepare("SELECT id FROM dim_participants WHERE cedula = ?");
        $check->execute([$data->cedula]);
        if ($check->rowCount() > 0) {
            http_response_code(409);
            echo json_encode(["error" => "Esta cédula ya está registrada en el evento DIM."]);
            exit();
        }

        $sql = "INSERT INTO dim_participants (cedula, full_name, email, phone, institution, state, city) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $data->cedula ?? '', 
            $data->fullName ?? '', 
            $data->email ?? '', 
            $data->phone ?? '', 
            $data->institution ?? '', 
            $data->state ?? '', 
            $data->city ?? ''
        ]);

        http_response_code(201);
        echo json_encode(["message" => "Pre-inscripción al DIM exitosa"]);

    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error interno: " . $e->getMessage()]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'check_certificate') {
    $cedula = isset($_GET['cedula']) ? $_GET['cedula'] : '';

    if (empty($cedula)) {
        http_response_code(400);
        echo json_encode(["error" => "Debes ingresar una cédula."]);
        exit();
    }

    try {
        $stmt = $conn->prepare("SELECT full_name, has_paid FROM dim_participants WHERE cedula = ?");
        $stmt->execute([$cedula]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            http_response_code(404);
            echo json_encode(["error" => "No se encontró esta cédula en los registros del DIM."]);
            exit();
        }

        if ($user['has_paid'] == 0) {
            http_response_code(403);
            echo json_encode(["error" => "Pago no verificado en el sistema."]);
            exit();
        }

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "fullName" => $user['full_name']
        ]);

    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error interno: " . $e->getMessage()]);
    }
}

// --- RUTA: OBTENER MURAL DE PARTICIPANTES DIM ---
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get_dim_participants') {
    try {
        $sql = "SELECT full_name AS nombre, institution AS institucion
                FROM dim_participants
                ORDER BY registration_date DESC";

        $stmt = $conn->query($sql);
        $participantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($participantes);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error interno al cargar el mural DIM: " . $e->getMessage()]);
    }
    exit();
}

// ==========================================
// RUTAS DE ADMINISTRACIÓN SECRETA
// ==========================================

elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get_evem_participants') {
    try {
        $stmt = $conn->query("SELECT id, cedula, full_name, institution, course_preference, has_paid FROM participants ORDER BY id DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al obtener lista de EVEM"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'toggle_evem_payment') {
    $data = json_decode(file_get_contents("php://input"));
    if (!isset($data->id) || !isset($data->has_paid)) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos"]);
        exit();
    }
    try {
        $stmt = $conn->prepare("UPDATE participants SET has_paid = ? WHERE id = ?");
        $stmt->execute([$data->has_paid, $data->id]);
        echo json_encode(["success" => true]);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al actualizar pago"]);
    }
}

elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get_dim_admin_participants') {
    try {
        $stmt = $conn->query("SELECT id, cedula, full_name, email, institution, has_paid, DATE_FORMAT(registration_date, '%d/%m/%Y') as fecha FROM dim_participants ORDER BY registration_date DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al obtener lista"]);
    }
}

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

// --- RUTA: OBTENER EQUIPOS DEL FESTIVAL (PANEL ADMIN) ---
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get_festival_teams') {
    try {
        $stmt = $conn->query("SELECT * FROM festival_teams ORDER BY created_at DESC");
        http_response_code(200);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error interno: " . $e->getMessage()]);
    }
    exit();
}

// --- RUTA: CAMBIAR ESTATUS (PAGO/APROBADO) FESTIVAL ---
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'toggle_festival_status') {
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
    exit();
}

// --- RUTA: VALIDAR CERTIFICADO DEL FESTIVAL ---
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'check_festival_certificate') {
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
    exit();
}

// --- RUTA: OBTENER IMAGENES DINAMICAS PARA CARRUSELES ---
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get_carousel_images') {
    $folder = isset($_GET['folder']) ? $_GET['folder'] : '';
    
    // Por seguridad, solo permitimos leer estas dos carpetas
    if ($folder === 'carruseluno' || $folder === 'carruseldos') {
        $directory = __DIR__ . '/../assets/images/' . $folder . '/';
        $images = [];
        
        if (is_dir($directory)) {
            $files = scandir($directory);
            foreach ($files as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                // Filtramos para que solo lea archivos de imagen
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $images[] = "../assets/images/" . $folder . "/" . $file;
                }
            }
        }
        http_response_code(200);
        echo json_encode($images);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Carpeta no permitida"]);
    }
    exit();
}

// --- RESPUESTA POR DEFECTO ---
else {
    echo json_encode(["status" => "API PHP de EVEM y DIM Activa y Operativa"]);
}
?>