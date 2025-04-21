<?php
session_start();
require '../config/connection.php';

$db = new Database();
$con = $db->conectar();

$response = array();


//$correo_permitido = 'ph9357480@gmail.com';
$correos_permitidos = ['armando@comtermexico.com', 'daniel@comtermexico.com', 'excitingnobel7@tomorjerry.com'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    try {

        /*if ($email !== $correo_permitido) {
            $response = [
                'status' => 'error',
                'message' => 'No tienes permitido ingresar como administrador.'
            ];
            echo json_encode($response);
            exit();
        }*/
        if (!in_array($email, $correos_permitidos)) {
            $response = [
                'status' => 'error',
                'message' => 'No tienes permitido ingresar como administrador.'
            ];
            echo json_encode($response);
            exit();
        }
        

        $sql = "SELECT * FROM administrador WHERE correo = :email";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userInfo) {
            if (password_verify($password, $userInfo['password'])) {
                if ((int) $userInfo['verificado'] === 1) {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['id_administrador'] = $userInfo['id_administrador'];
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
