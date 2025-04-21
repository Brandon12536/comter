<?php
session_start();

header('Content-Type: application/json');

require_once '../../config/connection.php'; 

if (!isset($_SESSION['id_administrador'])) {
    echo json_encode(['error' => 'No tiene autorización para realizar esta acción']);
    exit();
}

if (!isset($_GET['id_usuarios'])) {
    echo json_encode(['error' => 'No se proporcionó un ID válido']);
    exit();
}

try {
    $db = new Database();
    $con = $db->conectar();
    
    $id_usuarios = $_GET['id_usuarios'];
    
   
    $sql = "SELECT 
                u.id_usuarios,
                u.compania,
                u.business_unit,
                u.nombre,
                u.apellido,
                u.telefono,
                u.correo,
                u.role,
                u.created_at,
                rp.permiso_ver,
                rp.permiso_editar,
                rp.permiso_capturar
            FROM usuarios u
            LEFT JOIN roles_permisos_usuarios rp ON u.id_usuarios = rp.id_usuarios
            WHERE u.id_usuarios = :id_usuarios";
    
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':id_usuarios', $id_usuarios, PDO::PARAM_INT);
    $stmt->execute();
    
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$resultado) {
        echo json_encode(['error' => 'No se encontró el registro']);
        exit();
    }
    

    $resultado['created_at'] = date("l d F Y", strtotime($resultado['created_at'])); // Formato: "lunes 20 marzo 2025"
    
   
    $resultado['permiso_ver'] = (bool)$resultado['permiso_ver'];
    $resultado['permiso_editar'] = (bool)$resultado['permiso_editar'];
    $resultado['permiso_capturar'] = (bool)$resultado['permiso_capturar'];
    
    echo json_encode($resultado);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
