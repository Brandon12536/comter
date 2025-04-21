<?php
session_start();
//error_reporting(0);
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


    $id_usuarios = $_POST['id_usuarios'];


    $sqlUsuario = "UPDATE usuarios SET 
        compania = :compania,
        business_unit = :business_unit,
        nombre = :nombre,
        apellido = :apellido,
        telefono = :telefono,
        correo = :correo
        WHERE id_usuarios = :id_usuarios";

    $stmtUsuario = $con->prepare($sqlUsuario);
    $stmtUsuario->bindParam(':compania', $_POST['compania'], PDO::PARAM_STR);
    $stmtUsuario->bindParam(':business_unit', $_POST['business_unit'], PDO::PARAM_STR);
    $stmtUsuario->bindParam(':nombre', $_POST['nombre'], PDO::PARAM_STR);
    $stmtUsuario->bindParam(':apellido', $_POST['apellido'], PDO::PARAM_STR);
    $stmtUsuario->bindParam(':telefono', $_POST['telefono'], PDO::PARAM_STR);
    $stmtUsuario->bindParam(':correo', $_POST['correo'], PDO::PARAM_STR);
    $stmtUsuario->bindParam(':id_usuarios', $id_usuarios, PDO::PARAM_INT);
    $stmtUsuario->execute();


    $sqlCheckPermisos = "SELECT COUNT(*) as count FROM roles_permisos_usuarios WHERE id_usuarios = :id_usuarios";
    $stmtCheckPermisos = $con->prepare($sqlCheckPermisos);
    $stmtCheckPermisos->bindParam(':id_usuarios', $id_usuarios, PDO::PARAM_INT);
    $stmtCheckPermisos->execute();
    $permisosExisten = $stmtCheckPermisos->fetch(PDO::FETCH_ASSOC)['count'] > 0;


    $permiso_ver = isset($_POST['permiso_ver']) ? 1 : 0;
    $permiso_editar = isset($_POST['permiso_editar']) ? 1 : 0;
    $permiso_capturar = isset($_POST['permiso_capturar']) ? 1 : 0;


    if ($permisosExisten) {

        $sqlPermisos = "UPDATE roles_permisos_usuarios SET 
            permiso_ver = :permiso_ver,
            permiso_editar = :permiso_editar,
            permiso_capturar = :permiso_capturar,
            asignado_por = :id_administrador
            WHERE id_usuarios = :id_usuarios";
    } else {

        $sqlPermisos = "INSERT INTO roles_permisos_usuarios 
            (id_usuarios, permiso_ver, permiso_editar, permiso_capturar, asignado_por) 
            VALUES 
            (:id_usuarios, :permiso_ver, :permiso_editar, :permiso_capturar, :id_administrador)";
    }

    $stmtPermisos = $con->prepare($sqlPermisos);
    $stmtPermisos->bindParam(':id_usuarios', $id_usuarios, PDO::PARAM_INT);
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
