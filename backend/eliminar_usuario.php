<?php
session_start();
require '../config/connection.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
    exit;
}

$id = $_GET['id'];
$db = new Database();
$con = $db->conectar();

try {
    $sql = "DELETE FROM usuarios WHERE id_usuarios = :id";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se encontró el usuario']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al eliminar el usuario']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>
