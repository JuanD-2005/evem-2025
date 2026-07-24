<?php
// api.php - Backend Oficial EVEM & DIM 2026 (PHP Nativo) - Router Central

// 1. Configuración de Cabeceras (Permitir conexiones)
// Reflejamos el Origin de la petición en vez de "*": los navegadores rechazan
// combinar credentials:'include' (necesario para la cookie de sesión del admin)
// con Access-Control-Allow-Origin: *.
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '') {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
} else {
    header("Access-Control-Allow-Origin: *");
}
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Apagar la salida de advertencias HTML para no romper el JSON del frontend
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Manejo de petición preliminar (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Sesión segura para el panel administrativo
// 'secure' se activa solo cuando la petición ya llega por HTTPS: así la cookie
// funciona hoy (HTTP en dev/hosting universitario) y se endurece sola en cuanto
// el dominio de producción tenga HTTPS activo.
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || ($_SERVER['SERVER_PORT'] ?? '') == 443;

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_name('EVEM_ADMIN_SESSION');
session_start();

// 3. Configuración de Base de Datos y conexión PDO
$conn = require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';

// 4. Lógica de Enrutamiento
$action = isset($_GET['action']) ? $_GET['action'] : '';

$routes = [
    // Admin - Autenticación
    'admin_login'  => ['method' => 'POST', 'controller' => 'adminController.php', 'function' => 'admin_login'],
    'admin_logout' => ['method' => 'POST', 'controller' => 'adminController.php', 'function' => 'admin_logout'],

    // EVEM
    'courses' => ['method' => 'GET', 'controller' => 'evemController.php', 'function' => 'courses'],
    'register' => ['method' => 'POST', 'controller' => 'evemController.php', 'function' => 'register'],
    'check_evem_certificate' => ['method' => 'GET', 'controller' => 'evemController.php', 'function' => 'check_evem_certificate'],
    
    // DIM
    'register_dim_team' => ['method' => 'POST', 'controller' => 'dimController.php', 'function' => 'register_dim_team'],
    'get_dim_team' => ['method' => 'GET', 'controller' => 'dimController.php', 'function' => 'get_dim_team'],
    'get_participants' => ['method' => 'GET', 'controller' => 'dimController.php', 'function' => 'get_participants'],
    'register_dim' => ['method' => 'POST', 'controller' => 'dimController.php', 'function' => 'register_dim'],
    'check_certificate' => ['method' => 'GET', 'controller' => 'dimController.php', 'function' => 'check_certificate'],
    'get_dim_participants' => ['method' => 'GET', 'controller' => 'dimController.php', 'function' => 'get_dim_participants'],
    
    // Festival
    'register_festival' => ['method' => 'POST', 'controller' => 'festivalController.php', 'function' => 'register_festival'],
    'get_festival_teams' => ['method' => 'GET', 'controller' => 'festivalController.php', 'function' => 'get_festival_teams'],
    'toggle_festival_status' => ['method' => 'POST', 'controller' => 'festivalController.php', 'function' => 'toggle_festival_status'],
    'check_festival_certificate' => ['method' => 'GET', 'controller' => 'festivalController.php', 'function' => 'check_festival_certificate'],
    
    // Admin
    'get_evem_participants' => ['method' => 'GET', 'controller' => 'adminController.php', 'function' => 'get_evem_participants'],
    'toggle_evem_payment' => ['method' => 'POST', 'controller' => 'adminController.php', 'function' => 'toggle_evem_payment'],
    'get_dim_admin_participants' => ['method' => 'GET', 'controller' => 'adminController.php', 'function' => 'get_dim_admin_participants'],
    'toggle_payment' => ['method' => 'POST', 'controller' => 'adminController.php', 'function' => 'toggle_payment'],
    'delete_evem_participant' => ['method' => 'POST', 'controller' => 'adminController.php', 'function' => 'delete_evem_participant'],
    'delete_dim_participant' => ['method' => 'POST', 'controller' => 'adminController.php', 'function' => 'delete_dim_participant'],
    'delete_festival_team' => ['method' => 'POST', 'controller' => 'adminController.php', 'function' => 'delete_festival_team'],
    
    // Media / Uploads / Posters / Carousels
    'get_posters' => ['method' => 'GET', 'controller' => 'mediaController.php', 'function' => 'get_posters'],
    'get_carousel_images' => ['method' => 'GET', 'controller' => 'mediaController.php', 'function' => 'get_carousel_images'],
    'confirm_payment' => ['method' => 'POST', 'controller' => 'mediaController.php', 'function' => 'confirm_payment'],
];

if (array_key_exists($action, $routes) && $_SERVER['REQUEST_METHOD'] === $routes[$action]['method']) {
    require_once __DIR__ . '/controllers/' . $routes[$action]['controller'];
    $functionName = $routes[$action]['function'];
    $functionName($conn);
} else {
    echo json_encode(["status" => "API PHP de EVEM y DIM Activa y Operativa"]);
}
