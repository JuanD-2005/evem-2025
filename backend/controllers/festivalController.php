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
