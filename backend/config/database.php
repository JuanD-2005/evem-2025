<?php
// backend/config/database.php - Configuración de la base de datos

// Función para cargar variables del .env
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignorar comentarios
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Separar nombre y valor
        list($name, $value) = explode('=', $line, 2);
        
        // Limpiar espacios y guardar en la superglobal $_ENV
        $_ENV[trim($name)] = trim($value);
    }
    return true;
}

// Cargar el .env que está dos carpetas más atrás (en la raíz del proyecto)
loadEnv(__DIR__ . '/../../.env');

// Asignar las variables (con un valor por defecto como plan B de seguridad)
$host     = $_ENV['DB_HOST'] ?? 'localhost';
$db_name  = $_ENV['DB_NAME'] ?? 'evem_2025';
$username = $_ENV['DB_USER'] ?? 'root';
$password = $_ENV['DB_PASS'] ?? '';

try {
    $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name . ";charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión: " . $exception->getMessage()]);
    exit();
}

return $conn;
