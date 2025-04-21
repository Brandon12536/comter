<?php
session_start();
require '../../config/connection.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['semana'])) {
        throw new Exception('No se proporcionó una semana para verificar');
    }

    $semana = $_GET['semana'];
    
    $db = new Database();
    $con = $db->conectar();

    // Verificar si la semana existe
    $stmt = $con->prepare("SELECT id_version FROM versiones_inspeccion WHERE nombre_version = ?");
    $stmt->execute([$semana]);
    
    echo json_encode([
        'success' => true,
        'exists' => $stmt->rowCount() > 0
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
