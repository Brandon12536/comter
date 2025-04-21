<?php

session_start();


require '../config/connection.php';

$db = new Database();
$con = $db->conectar();


$codigo_verificacion = $_POST['codigo_verificacion'];


$correo = $_SESSION['correo'];
$id_proveedor = $_SESSION['id_proveedor'];


$sql = "SELECT codigo_verificacion FROM proveedores WHERE correo = :correo AND id_proveedor = :id_proveedor";
$stmt = $con->prepare($sql);
$stmt->bindParam(':correo', $correo);
$stmt->bindParam(':id_proveedor', $id_proveedor, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['codigo_verificacion'] === $codigo_verificacion) {
      
        echo json_encode(['success' => true, 'message' => 'Código verificado correctamente']);
    } else {
       
        echo json_encode(['success' => false, 'message' => 'Código incorrecto']);
    }
} else {
   
    echo json_encode(['success' => false, 'message' => 'No se encontró el correo o el proveedor en nuestros registros']);
}

$con = null;

