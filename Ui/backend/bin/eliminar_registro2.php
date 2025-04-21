<?php
session_start();

header('Content-Type: application/json');

require_once '../../config/connection.php';

if (!isset($_SESSION['id_administrador'])) {
    echo json_encode(['error' => 'No tiene autorización para realizar esta acción']);
    exit();
}

if (!isset($_GET['id_proveedor'])) {
    echo json_encode(['error' => 'No se proporcionó un ID válido']);
    exit();
}

try {
    $db = new Database();
    $con = $db->conectar();
    
    $con->beginTransaction();
    
    $sqlDeletePermisos = "DELETE FROM roles_permisos WHERE id_proveedor = :id_proveedor";
    $stmtDeletePermisos = $con->prepare($sqlDeletePermisos);
    $stmtDeletePermisos->bindParam(':id_proveedor', $_GET['id_proveedor'], PDO::PARAM_INT);
    $stmtDeletePermisos->execute();

    $sqlDeletePCBA = "DELETE FROM PCBA WHERE user_id = :id_proveedor";
    $stmtDeletePCBA = $con->prepare($sqlDeletePCBA);
    $stmtDeletePCBA->bindParam(':id_proveedor', $_GET['id_proveedor'], PDO::PARAM_INT);
    $stmtDeletePCBA->execute();

    $sqlDeleteMolex = "DELETE FROM molex WHERE id_proveedor = :id_proveedor";
    $stmtDeleteMolex = $con->prepare($sqlDeleteMolex);
    $stmtDeleteMolex->bindParam(':id_proveedor', $_GET['id_proveedor'], PDO::PARAM_INT);
    $stmtDeleteMolex->execute();

    $sqlDeleteInspecciones = "DELETE FROM inspecciones WHERE id_proveedor = :id_proveedor";
    $stmtDeleteInspecciones = $con->prepare($sqlDeleteInspecciones);
    $stmtDeleteInspecciones->bindParam(':id_proveedor', $_GET['id_proveedor'], PDO::PARAM_INT);
    $stmtDeleteInspecciones->execute();

    $sqlDeleteMateriales = "DELETE FROM materiales WHERE id_proveedor = :id_proveedor";
    $stmtDeleteMateriales = $con->prepare($sqlDeleteMateriales);
    $stmtDeleteMateriales->bindParam(':id_proveedor', $_GET['id_proveedor'], PDO::PARAM_INT);
    $stmtDeleteMateriales->execute();

    $sqlProveedor = "DELETE FROM proveedores WHERE id_proveedor = :id_proveedor";
    $stmtProveedor = $con->prepare($sqlProveedor);
    $stmtProveedor->bindParam(':id_proveedor', $_GET['id_proveedor'], PDO::PARAM_INT);
    $stmtProveedor->execute();

    if ($stmtProveedor->rowCount() == 0) {
        throw new Exception("No se pudo eliminar el proveedor.");
    }

    $sqlGetTurnos = "SELECT DISTINCT t.id_turno, t.nombre_turno 
                     FROM proveedores p 
                     JOIN turnos t ON p.id_turno = t.id_turno 
                     WHERE p.id_proveedor = :id_proveedor";
    $stmtGetTurnos = $con->prepare($sqlGetTurnos);
    $stmtGetTurnos->bindParam(':id_proveedor', $_GET['id_proveedor'], PDO::PARAM_INT);
    $stmtGetTurnos->execute();
    $turnosData = $stmtGetTurnos->fetchAll(PDO::FETCH_ASSOC);

    foreach ($turnosData as $turno) {
        $sqlCheckTurno = "SELECT COUNT(*) as count 
                         FROM proveedores 
                         WHERE id_turno = :id_turno";
        $stmtCheckTurno = $con->prepare($sqlCheckTurno);
        $stmtCheckTurno->bindParam(':id_turno', $turno['id_turno'], PDO::PARAM_INT);
        $stmtCheckTurno->execute();
        $countResult = $stmtCheckTurno->fetch(PDO::FETCH_ASSOC);
        
        if ($countResult['count'] == 0) {
            $sqlTurno = "DELETE FROM turnos 
                        WHERE id_turno = :id_turno 
                        AND nombre_turno IN ('1er.', '2do.', '3er.')";
            $stmtTurno = $con->prepare($sqlTurno);
            $stmtTurno->bindParam(':id_turno', $turno['id_turno'], PDO::PARAM_INT);
            $stmtTurno->execute();
        }
    }

    $con->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Proveedor y registros relacionados eliminados correctamente'
    ]);

} catch (Exception $e) {
    if ($con->inTransaction()) {
        $con->rollBack();
    }
    echo json_encode([
        'error' => 'Error al eliminar los registros: ' . $e->getMessage()
    ]);
}
