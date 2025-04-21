<?php
session_start();
error_reporting(0);
header('Content-Type: application/json');

require_once '../../config/connection.php';

if (!isset($_SESSION['id_administrador'])) {
    echo json_encode(['error' => 'No tiene autorización para realizar esta acción']);
    exit();
}

try {
    $db = new Database();
    $con = $db->conectar();


    $con->beginTransaction();


    $sqlGetTurno = "SELECT id_turno FROM turnos WHERE nombre_turno = :nombre_turno";
    $stmtGetTurno = $con->prepare($sqlGetTurno);
    $stmtGetTurno->bindParam(':nombre_turno', $_POST['turno_completo']);
    $stmtGetTurno->execute();

    $turnoData = $stmtGetTurno->fetch(PDO::FETCH_ASSOC);

    if (!$turnoData) {

        $sqlInsertTurno = "INSERT INTO turnos (nombre_turno, hora_inicio, hora_fin) 
                          VALUES (:nombre_turno, :hora_inicio, :hora_fin)";
        $stmtInsertTurno = $con->prepare($sqlInsertTurno);
        $stmtInsertTurno->bindParam(':nombre_turno', $_POST['turno_completo']);
        $stmtInsertTurno->bindParam(':hora_inicio', $_POST['hora_inicio']);
        $stmtInsertTurno->bindParam(':hora_fin', $_POST['hora_fin']);
        $stmtInsertTurno->execute();

        $id_turno = $con->lastInsertId();
    } else {
        $id_turno = $turnoData['id_turno'];


        $sqlUpdateTurno = "UPDATE turnos 
                          SET hora_inicio = :hora_inicio,
                              hora_fin = :hora_fin
                          WHERE id_turno = :id_turno";
        $stmtUpdateTurno = $con->prepare($sqlUpdateTurno);
        $stmtUpdateTurno->bindParam(':hora_inicio', $_POST['hora_inicio']);
        $stmtUpdateTurno->bindParam(':hora_fin', $_POST['hora_fin']);
        $stmtUpdateTurno->bindParam(':id_turno', $id_turno);
        $stmtUpdateTurno->execute();
    }


    $sqlProveedor = "UPDATE proveedores SET 
        compania = :compania,
        business_unit = :business_unit,
        nombre = :nombre,
        apellido = :apellido,
        telefono = :telefono,
        correo = :correo,
        departamento = :departamento,
        puesto = :puesto,
        id_turno = :id_turno
        WHERE id_proveedor = :id_proveedor";

    $stmtProveedor = $con->prepare($sqlProveedor);


    $stmtProveedor->bindParam(':compania', $_POST['compania'], PDO::PARAM_STR);
    $stmtProveedor->bindParam(':business_unit', $_POST['business_unit'], PDO::PARAM_STR);
    $stmtProveedor->bindParam(':nombre', $_POST['nombre'], PDO::PARAM_STR);
    $stmtProveedor->bindParam(':apellido', $_POST['apellido'], PDO::PARAM_STR);
    $stmtProveedor->bindParam(':telefono', $_POST['telefono'], PDO::PARAM_STR);
    $stmtProveedor->bindParam(':correo', $_POST['correo'], PDO::PARAM_STR);
    $stmtProveedor->bindParam(':departamento', $_POST['departamento'], PDO::PARAM_STR);
    $stmtProveedor->bindParam(':puesto', $_POST['puesto'], PDO::PARAM_STR);
    $stmtProveedor->bindParam(':id_turno', $id_turno, PDO::PARAM_INT);
    $stmtProveedor->bindParam(':id_proveedor', $_POST['id_proveedor'], PDO::PARAM_INT);

    $stmtProveedor->execute();


   
    $sqlCheckPermisos = "SELECT COUNT(*) as count FROM roles_permisos WHERE id_proveedor = :id_proveedor";
    $stmtCheckPermisos = $con->prepare($sqlCheckPermisos);
    $stmtCheckPermisos->bindParam(':id_proveedor', $_POST['id_proveedor'], PDO::PARAM_INT);
    $stmtCheckPermisos->execute();
    $permisosExisten = $stmtCheckPermisos->fetch(PDO::FETCH_ASSOC)['count'] > 0;

    
    $permiso_ver = isset($_POST['permiso_ver']) ? 1 : 0;
    $permiso_editar = isset($_POST['permiso_editar']) ? 1 : 0;
    $permiso_capturar = isset($_POST['permiso_capturar']) ? 1 : 0;

    if ($permisosExisten) {
      
        $sqlPermisos = "UPDATE roles_permisos SET 
            permiso_ver = :permiso_ver,
            permiso_editar = :permiso_editar,
            permiso_capturar = :permiso_capturar,
            asignado_por = :id_administrador
            WHERE id_proveedor = :id_proveedor";
    } else {
     
        $sqlPermisos = "INSERT INTO roles_permisos 
            (id_proveedor, permiso_ver, permiso_editar, permiso_capturar, asignado_por) 
            VALUES 
            (:id_proveedor, :permiso_ver, :permiso_editar, :permiso_capturar, :id_administrador)";
    }

    $stmtPermisos = $con->prepare($sqlPermisos);
    
 
    $stmtPermisos->bindParam(':id_proveedor', $_POST['id_proveedor'], PDO::PARAM_INT);
    $stmtPermisos->bindParam(':permiso_ver', $permiso_ver, PDO::PARAM_BOOL);
    $stmtPermisos->bindParam(':permiso_editar', $permiso_editar, PDO::PARAM_BOOL);
    $stmtPermisos->bindParam(':permiso_capturar', $permiso_capturar, PDO::PARAM_BOOL);
    $stmtPermisos->bindParam(':id_administrador', $_SESSION['id_administrador'], PDO::PARAM_INT);
    
    if (!$stmtPermisos->execute()) {
        throw new Exception('Error al actualizar los permisos');
    }


    $con->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Registro actualizado correctamente'
    ]);

} catch (Exception $e) {
    if ($con->inTransaction()) {
        $con->rollBack();
    }
    
    echo json_encode([
        'error' => 'Error al actualizar el registro: ' . $e->getMessage()
    ]);
}
