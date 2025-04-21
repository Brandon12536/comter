<?php
session_start();
require '../config/connection.php';

if (!isset($_SESSION['correo'])) {
    echo json_encode(['status' => 'error', 'message' => 'No se encontró un correo electrónico.']);
    exit();
}

$correo = $_SESSION['correo'];
$codigo_ingresado = isset($_POST['codigo_verificacion']) ? $_POST['codigo_verificacion'] : '';

if (empty($codigo_ingresado)) {
    echo json_encode(['status' => 'error', 'message' => 'Por favor ingresa un código de verificación.']);
    exit();
}

try {
    $db = new Database();
    $con = $db->conectar();

    
    $sql = "SELECT codigo_verificacion, verificado FROM proveedores WHERE correo = :correo";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':correo', $correo);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $codigo_db = $row['codigo_verificacion'];
        $verificado = $row['verificado'];

        if ($verificado == 1) {
            echo json_encode(['status' => 'error', 'message' => 'La cuenta ya está verificada.']);
            exit();
        }

        if ($codigo_db === $codigo_ingresado) {
           
            $sql_update = "UPDATE proveedores SET verificado = 1 WHERE correo = :correo";
            $stmt_update = $con->prepare($sql_update);
            $stmt_update->bindParam(':correo', $correo);
            $stmt_update->execute();

            echo json_encode(['status' => 'success', 'message' => 'Código verificado correctamente. ¡Bienvenido!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Código de verificación incorrecto.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se encontró el usuario.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    exit();
}
?>
