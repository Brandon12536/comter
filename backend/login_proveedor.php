<?php
session_start();
require '../config/connection.php';

$db = new Database();
$con = $db->conectar();

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    try {
        $sql = "SELECT * FROM proveedores WHERE correo = :email";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userInfo) {
            if (password_verify($password, $userInfo['password'])) {
                if ((int) $userInfo['verificado'] === 1) {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['id_proveedor'] = $userInfo['id_proveedor'];
                    $_SESSION['correo'] = $userInfo['correo'];
                    $_SESSION['role'] = $userInfo['role'];

                    $response = [
                        'status' => 'success',
                        'message' => 'Inicio de sesión exitoso.'
                    ];
                } else {
                    $response = [
                        'status' => 'error',
                        'message' => 'Cuenta no verificada.'
                    ];
                }
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Contraseña incorrecta.'
                ];
            }
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Correo electrónico no registrado.'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'status' => 'error',
            'message' => 'Error de conexión: ' . $e->getMessage()
        ];
    }

    echo json_encode($response);
    exit();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
    exit();
}
