<?php
session_start();
require '../config/connection.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID no proporcionado']);
    exit;
}

$id = $_GET['id'];
$db = new Database();
$con = $db->conectar();

try {
    $sql = "SELECT id_usuarios, compania, business_unit, nombre, apellido, correo, telefono 
            FROM usuarios 
            WHERE id_usuarios = :id";
    
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($usuario);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Usuario no encontrado']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>
