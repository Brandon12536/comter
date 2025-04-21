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


    $sql = "SELECT r.ruta_archivo, r.nombre_archivo
            FROM respaldos r
            WHERE r.id_respaldo = :id_respaldo";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':id_respaldo', $id_respaldo, PDO::PARAM_INT);
    $stmt->execute();


    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);


        $ruta_archivo = $row['ruta_archivo'];
        $nombre_archivo = $row['nombre_archivo'];


        if ($ruta_archivo !== null) {

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($nombre_archivo) . '"');
            header('Content-Length: ' . strlen($ruta_archivo));


            ob_clean();
            flush();


            echo $ruta_archivo;
            exit();
        } else {

            $_SESSION['error'] = "El archivo binario no está disponible.";
            header("Location: ../frontend/respaldos.php");
            exit();
        }
    } else {

        $_SESSION['error'] = "No se encontró el respaldo con el ID proporcionado.";
        header("Location: ../frontend/respaldos.php");
        exit();
    }
} else {

    $_SESSION['error'] = "No se ha proporcionado un ID de respaldo válido.";
    header("Location: ../frontend/respaldos.php");
    exit();
}

