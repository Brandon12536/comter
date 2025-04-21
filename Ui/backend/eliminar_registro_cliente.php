<?php
session_start();

header('Content-Type: application/json');

require_once '../../config/connection.php';

if (!isset($_SESSION['id_administrador'])) {
    echo json_encode(['error' => 'No tiene autorización para realizar esta acción']);
    exit();
}

if (!isset($_GET['id_usuarios']) || empty($_GET['id_usuarios'])) {
    echo json_encode(['error' => 'No se proporcionó un ID válido']);
    exit();
}

try {
    $db = new Database();
    $con = $db->conectar();

    $idUsuario = $_GET['id_usuarios'];


    $sqlCheckExistence = "SELECT * FROM usuarios WHERE id_usuarios = :id";
    $stmtCheckExistence = $con->prepare($sqlCheckExistence);
    $stmtCheckExistence->bindParam(':id', $idUsuario, PDO::PARAM_INT);
    $stmtCheckExistence->execute();
    $usuario = $stmtCheckExistence->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        throw new Exception("El usuario con ID $idUsuario no existe.");
    }


    $usuarioData = [
        'id_usuarios' => $usuario['id_usuarios'],
        'compania' => $usuario['compania'],
        'business_unit' => $usuario['business_unit'],
        'nombre' => $usuario['nombre'],
        'apellido' => $usuario['apellido'],
        'telefono' => $usuario['telefono'],
        'correo' => $usuario['correo'],
        'role' => $usuario['role'],
        'created_at' => $usuario['created_at'],
    ];


    if (isset($_GET['confirmar']) && $_GET['confirmar'] == 1) {
        $con->beginTransaction();


        $sqlDeleteRolesPermisos = "DELETE FROM roles_permisos_usuarios WHERE id_usuarios = :id";
        $stmtDeleteRolesPermisos = $con->prepare($sqlDeleteRolesPermisos);
        $stmtDeleteRolesPermisos->bindParam(':id', $idUsuario, PDO::PARAM_INT);
        $stmtDeleteRolesPermisos->execute();

        $sqlUsuario = "DELETE FROM usuarios WHERE id_usuarios = :id";
        $stmtUsuario = $con->prepare($sqlUsuario);
        $stmtUsuario->bindParam(':id', $idUsuario, PDO::PARAM_INT);
        $stmtUsuario->execute();

        if ($stmtUsuario->rowCount() === 0) {
            throw new Exception("No se pudo eliminar el usuario.");
        }

        $con->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Registro eliminado correctamente'
        ]);
        exit();
    }


    echo json_encode([
        'warning' => true,
        'message' => '¿Estás seguro de eliminar al siguiente usuario?',
        'usuario_data' => $usuarioData
    ]);

} catch (Exception $e) {
    if ($con->inTransaction()) {
        $con->rollBack();
    }
    echo json_encode([
        'error' => 'Error al eliminar el registro: ' . $e->getMessage()
    ]);
}
?>