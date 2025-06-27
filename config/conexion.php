<?php
$server = "localhost";
$namedb = "gestion_notas";
$user = "root";
$password = "";

try {
    $conexion = new mysqli($server, $user, $password, $namedb);
    
    // Verificar conexión
    if ($conexion->connect_errno) {
        throw new Exception("Conexión fallida: " . $conexion->connect_error);
    }
    
    // Configurar charset a utf8
    $conexion->set_charset("utf8mb4");
    
} catch (Exception $e) {
    // Registrar el error y mostrar mensaje genérico
    error_log($e->getMessage());
    die("Error al conectar con la base de datos. Por favor intente más tarde.");
}
?>