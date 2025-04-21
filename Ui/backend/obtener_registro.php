<?php
session_start();

header('Content-Type: application/json');

require_once '../../config/connection.php'; 

if (!isset($_SESSION['id_administrador'])) {
    echo json_encode(['error' => 'No tiene autorización para realizar esta acción']);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No se proporcionó un ID válido']);
    exit();
}

try {
    $db = new Database();
    $con = $db->conectar();
    
    $id = $_GET['id'];
    
   
    $sql = "SELECT 
                p.id_proveedor,
                p.compania,
                p.business_unit,
                p.nombre,
                p.apellido,
                p.telefono,
                p.correo,
                p.departamento,
                p.puesto,
                t.nombre_turno,          
                t.hora_inicio,
                t.hora_fin,
                rp.permiso_ver,
                rp.permiso_editar,
                rp.permiso_capturar
            FROM proveedores p
            LEFT JOIN turnos t ON p.id_turno = t.id_turno
            LEFT JOIN roles_permisos rp ON p.id_proveedor = rp.id_proveedor
            WHERE p.id_proveedor = :id";
    
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$resultado) {
        echo json_encode(['error' => 'No se encontró el registro']);
        exit();
    }
    
    
    $resultado['hora_inicio'] = substr($resultado['hora_inicio'], 0, 5); 
    $resultado['hora_fin'] = substr($resultado['hora_fin'], 0, 5); 
    

    $resultado['permiso_ver'] = (bool)$resultado['permiso_ver'];
    $resultado['permiso_editar'] = (bool)$resultado['permiso_editar'];
    $resultado['permiso_capturar'] = (bool)$resultado['permiso_capturar'];
    
    
    $resultado['turno_completo'] = $resultado['nombre_turno']; 
    
    echo json_encode($resultado);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}

