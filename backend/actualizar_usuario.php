<?php
session_start();
require '../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['mensaje'] = "Método no permitido";
    $_SESSION['tipo_mensaje'] = "error";
    header('Location: ../Ui/administrador.php');
    exit();
}

$db = new Database();
$con = $db->conectar();

try {

    $campos_requeridos = ['id_usuario', 'compania', 'business_unit', 'nombre', 'apellido', 'correo', 'telefono'];
    foreach ($campos_requeridos as $campo) {
        if (empty($_POST[$campo])) {
            throw new Exception("El campo " . ucfirst($campo) . " es requerido");
        }
    }

   
    if (!filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("El formato del correo electrónico no es válido");
    }

   
    if (strlen($_POST['telefono']) < 10) {
        throw new Exception("El número de teléfono debe tener al menos 10 dígitos");
    }

    
    $sql = "SELECT COUNT(*) FROM usuarios WHERE correo = :correo AND id_usuarios != :id_usuario";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':correo', $_POST['correo'], PDO::PARAM_STR);
    $stmt->bindParam(':id_usuario', $_POST['id_usuario'], PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("El correo electrónico ya está registrado para otro usuario");
    }

   
    $sql = "UPDATE usuarios SET 
            compania = :compania,
            business_unit = :business_unit,
            nombre = :nombre,
            apellido = :apellido,
            correo = :correo,
            telefono = :telefono";

   
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql .= ", password = :password";
    }

    $sql .= " WHERE id_usuarios = :id_usuario";

    $stmt = $con->prepare($sql);
    $stmt->bindParam(':compania', $_POST['compania'], PDO::PARAM_STR);
    $stmt->bindParam(':business_unit', $_POST['business_unit'], PDO::PARAM_STR);
    $stmt->bindParam(':nombre', $_POST['nombre'], PDO::PARAM_STR);
    $stmt->bindParam(':apellido', $_POST['apellido'], PDO::PARAM_STR);
    $stmt->bindParam(':correo', $_POST['correo'], PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $_POST['telefono'], PDO::PARAM_STR);
    $stmt->bindParam(':id_usuario', $_POST['id_usuario'], PDO::PARAM_INT);

    if (!empty($_POST['password'])) {
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    }

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Usuario actualizado correctamente";
        $_SESSION['tipo_mensaje'] = "success";
    } else {
        throw new Exception("Error al actualizar el usuario");
    }

} catch (Exception $e) {
    $_SESSION['mensaje'] = $e->getMessage();
    $_SESSION['tipo_mensaje'] = "error";
}

header('Location: ../Ui/administrador.php');
exit();
?>
