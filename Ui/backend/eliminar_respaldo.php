<?php
session_start();

if (!isset($_SESSION['id_administrador'])) {
    echo 'La sesión no tiene el id_administrador.';
    exit();
}

$id_administrador = $_SESSION['id_administrador'];

require '../../config/connection.php';

$db = new Database();
$con = $db->conectar();

if (isset($_GET['id_respaldo'])) {
    $id_respaldo = $_GET['id_respaldo'];

    $sql_check = "SELECT * FROM respaldos WHERE id_respaldo = :id_respaldo AND id_administrador = :id_administrador";
    $stmt_check = $con->prepare($sql_check);
    $stmt_check->bindParam(':id_respaldo', $id_respaldo, PDO::PARAM_INT);
    $stmt_check->bindParam(':id_administrador', $id_administrador, PDO::PARAM_INT);
    $stmt_check->execute();

    if ($stmt_check->rowCount() == 0) {
        $_SESSION['error'] = 'No se ha encontrado el respaldo que deseas eliminar.';
        header('Location: ../frontend/respaldos.php');
        exit();
    }

    try {
        $con->beginTransaction();

        $sql_delete_details = "DELETE FROM detalles_respaldo WHERE id_respaldo = :id_respaldo";
        $stmt_delete_details = $con->prepare($sql_delete_details);
        $stmt_delete_details->bindParam(':id_respaldo', $id_respaldo, PDO::PARAM_INT);
        $stmt_delete_details->execute();

        $sql = "DELETE FROM respaldos WHERE id_respaldo = :id_respaldo AND id_administrador = :id_administrador";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id_respaldo', $id_respaldo, PDO::PARAM_INT);
        $stmt->bindParam(':id_administrador', $id_administrador, PDO::PARAM_INT);
        $stmt->execute();

        $con->commit();
        $_SESSION['success'] = 'Respaldo eliminado con éxito';

    } catch (Exception $e) {
        $con->rollBack();
        $_SESSION['error'] = 'Error en la eliminación: ' . $e->getMessage();
    }

    header('Location: ../frontend/respaldos.php');
    exit;
} else {
    $_SESSION['error'] = 'No se ha proporcionado un respaldo para eliminar.';
    header('Location: ../frontend/respaldos.php');
    exit();
}
