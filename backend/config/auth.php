<?php
// backend/config/auth.php - Control de acceso al panel administrativo

// Arranca (o reutiliza) la sesión de administrador con los mismos
// parámetros de cookie en todo el proyecto. La llama api.php para las
// rutas del API y cualquier página PHP que necesite leer esa misma
// sesión fuera del router (ej. generar-festival-certificado.php) — si
// alguna de las dos cambiara sus propios parámetros, dejarían de ver
// la misma sesión aunque compartan navegador.
// Idempotente: si ya hay una sesión activa (p. ej. porque el propio
// api.php ya la abrió antes de incluir algo que vuelve a llamarla),
// no la reinicia.
function bootstrap_admin_session() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    // 'secure' se activa solo cuando la petición ya llega por HTTPS: así la
    // cookie funciona hoy (HTTP en dev/hosting universitario) y se
    // endurece sola en cuanto el dominio de producción tenga HTTPS activo.
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['SERVER_PORT'] ?? '') == 443;

    // Firma posicional (no la de array/opciones, que exige PHP 7.3+): el
    // hosting de producción corre PHP 7.0.33 (ver _phpcheck.php y el
    // comentario de footer.php), así que la de array tira fatal ahí. El
    // 'samesite' se cuela metiéndolo dentro de $path — mismo truco que usa
    // cualquier proyecto PHP <7.3 para setear ese atributo, porque el
    // navegador solo lee el header Set-Cookie final separado por ';' y no
    // le importa de qué parámetro salió cada trozo.
    session_set_cookie_params(
        0,                        // lifetime
        '/; samesite=Lax',        // path + hack de samesite
        '',                       // domain
        $isHttps,                 // secure
        true                      // httponly
    );
    session_name('EVEM_ADMIN_SESSION');
    session_start();
}

// Corta la ejecución con 401 si no hay sesión de admin activa.
// Si se indica $panel, además exige que la sesión pertenezca a ese panel
// (evem/dim/festival), igual que antes cada panel tenía su propio PIN.
//
// $redirectTo es para páginas HTML servidas directamente (no rutas del
// API): en vez del 401/403 crudo en JSON, que no tiene sentido devolverle
// a un navegador navegando una página, redirige a esa URL. Las rutas del
// API (festivalController.php y demás) no lo pasan y siguen recibiendo
// el JSON de siempre.
function require_admin_session($panel = null, $redirectTo = null) {
    if (empty($_SESSION['admin_logged_in'])) {
        if ($redirectTo !== null) {
            header("Location: $redirectTo");
            exit();
        }
        http_response_code(401);
        echo json_encode(["error" => "No autorizado. Debes iniciar sesión como administrador."]);
        exit();
    }

    if ($panel !== null && ($_SESSION['admin_panel'] ?? null) !== $panel) {
        if ($redirectTo !== null) {
            header("Location: $redirectTo");
            exit();
        }
        http_response_code(403);
        echo json_encode(["error" => "No tienes permisos para administrar este panel."]);
        exit();
    }
}
