<?php
// backend/controllers/evemController.php

function courses($conn) {
    try {
        $stmt = $conn->prepare("SELECT * FROM courses WHERE is_active = TRUE");
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al cargar cursos"]);
    }
}

function register($conn) {
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

function check_evem_certificate($conn) {
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
