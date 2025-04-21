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

if (isset($_GET['confirmar']) && $_GET['confirmar'] == '1') {
    try {
        $db = new Database();
        $con = $db->conectar();
        
        $con->beginTransaction();

        
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

        $sqlPermisos = "DELETE FROM roles_permisos WHERE id_proveedor = :id_proveedor";
        $stmtPermisos = $con->prepare($sqlPermisos);
        $stmtPermisos->bindParam(':id_proveedor', $_GET['id_proveedor'], PDO::PARAM_INT);
        $stmtPermisos->execute();

        
        $sqlGetTurnos = "SELECT DISTINCT t.id_turno, t.nombre_turno 
                         FROM proveedores p 
                         JOIN turnos t ON p.id_turno = t.id_turno 
                         WHERE p.id_proveedor = :id_proveedor";
        $stmtGetTurnos = $con->prepare($sqlGetTurnos);
        $stmtGetTurnos->bindParam(':id_proveedor', $_GET['id_proveedor'], PDO::PARAM_INT);
        $stmtGetTurnos->execute();
        $turnosData = $stmtGetTurnos->fetchAll(PDO::FETCH_ASSOC);

        $sqlProveedor = "DELETE FROM proveedores WHERE id_proveedor = :id_proveedor";
        $stmtProveedor = $con->prepare($sqlProveedor);
        $stmtProveedor->bindParam(':id_proveedor', $_GET['id_proveedor'], PDO::PARAM_INT);
        $stmtProveedor->execute();

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
            'message' => 'Registro eliminado correctamente'
        ]);
    } catch (Exception $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }
        echo json_encode([
            'error' => 'Error al eliminar el registro: ' . $e->getMessage()
        ]);
    }
} else {
   
    try {
        $db = new Database();
        $con = $db->conectar();
        
     
        $registrosRelacionados = [];
        
       
        $sqlPCBA = "SELECT COUNT(*) as total FROM PCBA WHERE user_id = :id_proveedor";
        $stmtPCBA = $con->prepare($sqlPCBA);
        $stmtPCBA->bindParam(':id_proveedor', $_GET['id_proveedor'], PDO::PARAM_INT);
        $stmtPCBA->execute();
        $totalPCBA = $stmtPCBA->fetch(PDO::FETCH_ASSOC)['total'];
        if ($totalPCBA > 0) {
            $registrosRelacionados['PCBA'] = $totalPCBA;
        }
        
        $sqlMolex = "SELECT COUNT(*) as total FROM molex WHERE id_proveedor = :id_proveedor";
        $stmtMolex = $con->prepare($sqlMolex);
        $stmtMolex->bindParam(':id_proveedor', $_GET['id_proveedor'], PDO::PARAM_INT);
        $stmtMolex->execute();
        $totalMolex = $stmtMolex->fetch(PDO::FETCH_ASSOC)['total'];
        if ($totalMolex > 0) {
            $registrosRelacionados['Molex'] = $totalMolex;
        }

        $sqlInspecciones = "SELECT COUNT(*) as total FROM inspecciones WHERE id_proveedor = :id_proveedor";
        $stmtInspecciones = $con->prepare($sqlInspecciones);
        $stmtInspecciones->bindParam(':id_proveedor', $_GET['id_proveedor'], PDO::PARAM_INT);
        $stmtInspecciones->execute();
        $totalInspecciones = $stmtInspecciones->fetch(PDO::FETCH_ASSOC)['total'];
        if ($totalInspecciones > 0) {
            $registrosRelacionados['Inspecciones'] = $totalInspecciones;
        }
        
        $sqlMateriales = "SELECT COUNT(*) as total FROM materiales WHERE id_proveedor = :id_proveedor";
        $stmtMateriales = $con->prepare($sqlMateriales);
        $stmtMateriales->bindParam(':id_proveedor', $_GET['id_proveedor'], PDO::PARAM_INT);
        $stmtMateriales->execute();
        $totalMateriales = $stmtMateriales->fetch(PDO::FETCH_ASSOC)['total'];
        if ($totalMateriales > 0) {
            $registrosRelacionados['Materiales'] = $totalMateriales;
        }

        if (!empty($registrosRelacionados)) {
            $mensaje = "¡Advertencia! Al eliminar este usuario también se eliminarán los siguientes registros:\n";
            foreach ($registrosRelacionados as $tabla => $total) {
                $mensaje .= "- $tabla: $total registros\n";
            }

            echo json_encode([
                'warning' => true,
                'message' => $mensaje
            ]);
        } else {
            echo json_encode([
                'warning' => false,
                'message' => 'Este usuario no tiene registros asociados en ninguna tabla.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'error' => 'Error al verificar registros relacionados: ' . $e->getMessage()
        ]);
    }
}
