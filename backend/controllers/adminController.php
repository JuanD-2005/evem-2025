<?php
// backend/controllers/adminController.php

function admin_login($conn) {
    $data = json_decode(file_get_contents("php://input"));
    $panel = isset($data->panel) ? strtolower(trim($data->panel)) : '';
    $pin = isset($data->pin) ? (string) $data->pin : '';

    $pinsPorPanel = [
        'evem'     => $_ENV['ADMIN_PIN_EVEM'] ?? null,
        'dim'      => $_ENV['ADMIN_PIN_DIM'] ?? null,
        'festival' => $_ENV['ADMIN_PIN_FESTIVAL'] ?? null,
    ];

    if (!array_key_exists($panel, $pinsPorPanel) || empty($pinsPorPanel[$panel])) {
        http_response_code(400);
        echo json_encode(["error" => "Panel administrativo inválido o no configurado."]);
        exit();
    }

    if (empty($pin) || !hash_equals($pinsPorPanel[$panel], $pin)) {
        http_response_code(401);
        echo json_encode(["error" => "PIN incorrecto."]);
        exit();
    }

    // Evita fijación de sesión: se emite un ID de sesión nuevo tras autenticar
    session_regenerate_id(true);
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_panel'] = $panel;

    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Acceso concedido."]);
}

function admin_logout($conn) {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();

    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Sesión cerrada."]);
}

function get_evem_participants($conn) {
    require_admin_session('evem');
    try {
        $stmt = $conn->query("SELECT id, cedula, full_name, institution, course_preference, has_paid FROM participants ORDER BY id DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al obtener lista de EVEM"]);
    }
}

function toggle_evem_payment($conn) {
    require_admin_session('evem');
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

function get_dim_admin_participants($conn) {
    require_admin_session('dim');
    try {
        $stmt = $conn->query("SELECT id, cedula, full_name, email, institution, has_paid, DATE_FORMAT(registration_date, '%d/%m/%Y') as fecha FROM dim_participants ORDER BY registration_date DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al obtener lista"]);
    }
}

function toggle_payment($conn) {
    require_admin_session('dim');
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

function delete_evem_participant($conn) {
    require_admin_session('evem');
    $data = json_decode(file_get_contents("php://input"));
    if (!isset($data->id)) {
        http_response_code(400);
        echo json_encode(["error" => "Falta el ID del participante"]);
        exit();
    }
    try {
        // Primero obtenemos el curso para decrementar el cupo
        $getUser = $conn->prepare("SELECT course_preference FROM participants WHERE id = ?");
        $getUser->execute([$data->id]);
        $user = $getUser->fetch(PDO::FETCH_ASSOC);

        // Eliminamos el participante
        $stmt = $conn->prepare("DELETE FROM participants WHERE id = ?");
        $stmt->execute([$data->id]);

        // Si había un curso asignado, decrementamos el cupo (sin bajar de 0)
        if ($user && !empty($user['course_preference'])) {
            $conn->prepare("UPDATE courses SET current_enrollment = GREATEST(current_enrollment - 1, 0) WHERE title = ?")
                 ->execute([$user['course_preference']]);
        }

        echo json_encode(["success" => true]);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al eliminar participante EVEM: " . $e->getMessage()]);
    }
}

function delete_dim_participant($conn) {
    require_admin_session('dim');
    $data = json_decode(file_get_contents("php://input"));
    if (!isset($data->id)) {
        http_response_code(400);
        echo json_encode(["error" => "Falta el ID del participante"]);
        exit();
    }
    try {
        $stmt = $conn->prepare("DELETE FROM dim_participants WHERE id = ?");
        $stmt->execute([$data->id]);
        echo json_encode(["success" => true]);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al eliminar participante DIM: " . $e->getMessage()]);
    }
}

function delete_festival_team($conn) {
    require_admin_session('festival');
    $data = json_decode(file_get_contents("php://input"));
    if (!isset($data->id)) {
        http_response_code(400);
        echo json_encode(["error" => "Falta el ID del equipo"]);
        exit();
    }
    try {
        $stmt = $conn->prepare("DELETE FROM festival_teams WHERE id = ?");
        $stmt->execute([$data->id]);
        echo json_encode(["success" => true]);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al eliminar equipo Festival: " . $e->getMessage()]);
    }
}
