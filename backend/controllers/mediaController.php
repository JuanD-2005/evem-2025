<?php
// backend/controllers/mediaController.php

function get_posters($conn) {
    try {
        // Buscamos en 'participants' y usamos los nombres reales de las columnas
        $stmt = $conn->query("SELECT full_name, institution, poster_title, poster_abstract FROM participants WHERE participation_type = 'poster' ORDER BY id DESC");
        $posters = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200);
        echo json_encode($posters);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al obtener los posters."]);
    }
}

function get_carousel_images($conn) {
    $folder = isset($_GET['folder']) ? $_GET['folder'] : '';
    
    // Por seguridad, solo permitimos leer carpetas aprobadas
    $allowedFolders = ['carruseluno', 'carruseldos', 'carruseltres', 'fotoscarrusel'];
    if (in_array($folder, $allowedFolders, true)) {
        $directory = __DIR__ . '/../../assets/carruseles/' . $folder . '/';
        $images = [];
        
        if (is_dir($directory)) {
            $files = scandir($directory);
            foreach ($files as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                // Filtramos para que solo lea archivos de imagen
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $images[] = "../assets/carruseles/" . $folder . "/" . $file;
                }
            }
        }
        http_response_code(200);
        echo json_encode($images);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Carpeta no permitida"]);
    }
}

function confirm_payment($conn) {
    // 1. Validar que llegaron los datos requeridos
    $cedula = isset($_POST['cedula']) ? trim($_POST['cedula']) : '';

    if (empty($cedula)) {
        http_response_code(400);
        echo json_encode(['error' => 'La cédula es obligatoria.']);
        exit();
    }

    if (!isset($_FILES['voucher']) || $_FILES['voucher']['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE   => 'El archivo supera el límite del servidor (upload_max_filesize).',
            UPLOAD_ERR_FORM_SIZE  => 'El archivo supera el límite del formulario.',
            UPLOAD_ERR_PARTIAL    => 'El archivo se subió de forma incompleta.',
            UPLOAD_ERR_NO_FILE    => 'No se recibió ningún archivo.',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal del servidor.',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en disco.',
            UPLOAD_ERR_EXTENSION  => 'Una extensión de PHP bloqueó la subida.',
        ];
        $errorCode = $_FILES['voucher']['error'] ?? UPLOAD_ERR_NO_FILE;
        $errorMsg  = $uploadErrors[$errorCode] ?? 'Error desconocido al subir el archivo.';
        http_response_code(400);
        echo json_encode(['error' => $errorMsg]);
        exit();
    }

    // 2. Verificar que el participante existe en la tabla
    try {
        $check = $conn->prepare('SELECT id FROM participants WHERE cedula = ?');
        $check->execute([$cedula]);
        if ($check->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'No se encontró ninguna inscripción con esa cédula. ¿Ya completaste la Pre-Inscripción?']);
            exit();
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al verificar la cédula: ' . $e->getMessage()]);
        exit();
    }

    // 3. Validar tipo MIME del archivo (whitelist estricta)
    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
    $finfo        = new finfo(FILEINFO_MIME_TYPE);
    $detectedMime = $finfo->file($_FILES['voucher']['tmp_name']);

    if (!in_array($detectedMime, $allowedMimes, true)) {
        http_response_code(400);
        echo json_encode(['error' => 'Tipo de archivo no permitido. Solo se aceptan imágenes (JPG, PNG, WEBP) o PDF.']);
        exit();
    }

    // 4. Crear la carpeta uploads/ si no existe (relativa al controlador, apuntando a backend/uploads/)
    $uploadsDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadsDir)) {
        if (!mkdir($uploadsDir, 0755, true)) {
            http_response_code(500);
            echo json_encode(['error' => 'No se pudo crear la carpeta de uploads en el servidor.']);
            exit();
        }
    }

    // 5. Generar nombre de archivo seguro y único
    $originalExt  = pathinfo($_FILES['voucher']['name'], PATHINFO_EXTENSION);
    $safeExt      = strtolower($originalExt);
    $safeFilename = 'bauche_' . preg_replace('/[^a-zA-Z0-9_\-]/', '', $cedula) . '_' . time() . '.' . $safeExt;
    $destination  = $uploadsDir . $safeFilename;

    // 6. Mover el archivo temporal a su destino definitivo
    if (!move_uploaded_file($_FILES['voucher']['tmp_name'], $destination)) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al guardar el archivo en el servidor. Verifica los permisos de la carpeta uploads/.']);
        exit();
    }

    // 7. Actualizar la BD: guardar la ruta del bauche y cambiar el estado a 'revision'
    try {
        $stmt = $conn->prepare(
            "UPDATE participants
             SET voucher_path    = ?,
                 payment_status  = 'revision'
             WHERE cedula = ?"
         );
         $stmt->execute(['uploads/' . $safeFilename, $cedula]);
 
         if ($stmt->rowCount() === 0) {
             // El archivo ya se subió pero el UPDATE no afectó filas (inesperado)
             http_response_code(404);
             echo json_encode(['error' => 'El comprobante se guardó pero no se pudo actualizar el registro. Contacta a soporte.']);
             exit();
         }
 
         http_response_code(200);
         echo json_encode([
             'message' => '¡Comprobante recibido! Tu pago quedó en estado de revisión. El equipo organizador lo verificará pronto.',
         ]);
 
     } catch (Exception $e) {
         // Si falla la BD, eliminar el archivo ya subido para no dejar basura
         @unlink($destination);
         http_response_code(500);
         echo json_encode(['error' => 'Error al actualizar el registro: ' . $e->getMessage()]);
     }
}
