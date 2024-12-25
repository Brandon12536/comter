<?php
session_start();
require '../phpmailer/src/Exception.php';
require '../config/connection.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

$db = new Database();
$con = $db->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $photo = $_FILES['photo']['tmp_name'];

    if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, completa todos los campos.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'El email no es válido.']);
        exit;
    }

    if ($password !== $confirmPassword) {
        echo json_encode(['status' => 'error', 'message' => 'Las contraseñas no coinciden.']);
        exit;
    }

    if (strlen($password) < 8 || !preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
        echo json_encode(['status' => 'error', 'message' => 'La contraseña debe tener al menos 8 caracteres y contener números y letras.']);
        exit;
    }

    $sql = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $emailExists = $stmt->fetchColumn();

    if ($emailExists) {
        echo json_encode(['status' => 'error', 'message' => 'El correo electrónico ya está registrado.']);
        exit;
    }

    $role = 'Cliente';
    $sql = "SELECT COUNT(*) FROM usuarios";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $userCount = $stmt->fetchColumn();

    if ($userCount == 0) {
        $role = 'Proveedor';
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $activationToken = bin2hex(random_bytes(32));


    if ($photo) {
        $photoContent = file_get_contents($photo);
    } else {
        $photoContent = null;
    }

    $sql = "INSERT INTO usuarios (firstname, lastname, email, password, role, photo, activation_token, active) 
            VALUES (:firstname, :lastname, :email, :password, :role, :photo, :activationToken, 0)";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':firstname', $firstname);
    $stmt->bindParam(':lastname', $lastname);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':photo', $photoContent, PDO::PARAM_LOB);
    $stmt->bindParam(':activationToken', $activationToken);

    if ($stmt->execute()) {
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->Username = 'bp754509@gmail.com';
        $mail->Password = 'qkse ycth akvp iqpa';

        $mail->setFrom('bp754509@gmail.com', 'COMTER');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Activación de cuenta en tu plataforma';
        $mail->AddEmbeddedImage('../ico/comter.png', 'logo_comter');
        $mail->Body = '
            <div style="background-color: #1b2455; padding: 20px; text-align: center; color: white;">
              <img src="cid:logo_comter" alt="" style="display: block; margin: 0 auto; width: 300px; height: 200px;">
                <h2 style="color: white;">¡Gracias por registrarte!</h2>
                <p style="color: white;">Para activar tu cuenta, haz clic en el siguiente enlace:</p>
                <a href="http://localhost/comter/backend/activar.php?token=' . urlencode($activationToken) . '" 
                style="color: #ffffff; background-color: #e74c3c; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                Activar cuenta
                </a>
            </div>';

        if ($mail->send()) {
            echo json_encode(['status' => 'success', 'message' => 'Te enviamos un correo para activar tu cuenta.']);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al enviar el correo de activación.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar el usuario en la base de datos.']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
    exit;
}

