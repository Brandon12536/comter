<?php

session_start();


require '../config/connection.php';

$db = new Database();
$con = $db->conectar();


$codigo_verificacion = $_POST['codigo_verificacion'];


$correo = $_SESSION['correo'];
$id_administrador = $_SESSION['id_administrador'];


$sql = "SELECT codigo_verificacion FROM administrador WHERE correo = :correo AND id_administrador = :id_administrador";
$stmt = $con->prepare($sql);
$stmt->bindParam(':correo', $correo);
$stmt->bindParam(':id_administrador', $id_administrador, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['codigo_verificacion'] === $codigo_verificacion) {
      
        echo json_encode(['success' => true, 'message' => 'Código verificado correctamente']);
    } else {
       
        echo json_encode(['success' => false, 'message' => 'Código incorrecto']);
    }
} else {
   
    echo json_encode(['success' => false, 'message' => 'No se encontró el correo o el administrador en nuestros registros']);
}

$con = null;

