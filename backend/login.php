<?php
session_start();
require '../config/connection.php';

$db = new Database();
$con = $db->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, completa todos los campos.']);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'El email no es válido.']);
        exit();
    }

    $sql = "SELECT id_usuarios, password, role, active FROM usuarios WHERE email = :email";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userInfo) {
        if (password_verify($password, $userInfo['password'])) {
            if ((int)$userInfo['active'] === 1) {
                
                $_SESSION['loggedin'] = true;
                $_SESSION['id_usuarios'] = $userInfo['id_usuarios'];
                $_SESSION['role'] = $userInfo['role'];

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Inicio de sesión exitoso.',
                    'role' => $userInfo['role'],
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Cuenta no activada.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Contraseña incorrecta.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Correo electrónico no registrado.']);
    }
    exit();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
    exit();
}
