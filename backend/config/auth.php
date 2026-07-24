<?php
// backend/config/auth.php - Control de acceso al panel administrativo

// Corta la ejecución con 401 si no hay sesión de admin activa.
// Si se indica $panel, además exige que la sesión pertenezca a ese panel
// (evem/dim/festival), igual que antes cada panel tenía su propio PIN.
function require_admin_session($panel = null) {
    if (empty($_SESSION['admin_logged_in'])) {
        http_response_code(401);
        echo json_encode(["error" => "No autorizado. Debes iniciar sesión como administrador."]);
        exit();
    }

    if ($panel !== null && ($_SESSION['admin_panel'] ?? null) !== $panel) {
        http_response_code(403);
        echo json_encode(["error" => "No tienes permisos para administrar este panel."]);
        exit();
    }
}
