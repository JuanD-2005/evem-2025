<?php
// backend/controllers/dimController.php

function register_dim_team($conn) {
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

function get_dim_team($conn) {
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
}

function get_participants($conn) {
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
}

function register_dim($conn) {
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

function check_certificate($conn) {
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

function get_dim_participants($conn) {
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
}
