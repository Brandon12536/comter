<?php
session_start();
require '../config/connection.php';
require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

$db = new Database();
$con = $db->conectar();
$con->exec("SET NAMES 'utf8'");

$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
$apellido = isset($_POST['apellido']) ? $_POST['apellido'] : '';
$compania = isset($_POST['compania']) ? $_POST['compania'] : '';
$business_unit = isset($_POST['business_unit']) ? $_POST['business_unit'] : '';
$telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
$correo = isset($_POST['correo']) ? $_POST['correo'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

if (empty($nombre) || empty($apellido) || empty($compania) || empty($business_unit) || empty($telefono) || empty($correo) || empty($password) || empty($confirm_password)) {
  echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios. Por favor, completa todos los campos.']);
  exit();
}


if ($password !== $confirm_password) {
    echo json_encode(['status' => 'error', 'message' => 'Las contraseñas no coinciden.']);
    exit();
}

$sql_check_email = "SELECT * FROM usuarios WHERE correo = :correo";
$stmt_check_email = $con->prepare($sql_check_email);
$stmt_check_email->bindParam(':correo', $correo);
$stmt_check_email->execute();

if ($stmt_check_email->rowCount() > 0) {
    echo json_encode(['status' => 'error', 'message' => 'El correo electrónico ya está registrado.']);
    exit();
}


$codigo_verificacion = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

try {
  
    $sql_insert_temp = "INSERT INTO usuarios (nombre, apellido, compania, business_unit, telefono, correo, codigo_verificacion, password, role, verificado)
                        VALUES (:nombre, :apellido, :compania, :business_unit, :telefono, :correo, :codigo_verificacion, :password, :role, 0)";
    $stmt_insert_temp = $con->prepare($sql_insert_temp);
    $stmt_insert_temp->bindParam(':nombre', $nombre);
    $stmt_insert_temp->bindParam(':apellido', $apellido);
    $stmt_insert_temp->bindParam(':compania', $compania);
    $stmt_insert_temp->bindParam(':business_unit', $business_unit);
    $stmt_insert_temp->bindParam(':telefono', $telefono);
    $stmt_insert_temp->bindParam(':correo', $correo);
    $stmt_insert_temp->bindParam(':codigo_verificacion', $codigo_verificacion);
    $stmt_insert_temp->bindParam(':password', $hashed_password);
    $stmt_insert_temp->bindParam(':role', $role);

    $role = 'Cliente'; 

    if ($stmt_insert_temp->execute()) {
        
        $_SESSION['correo'] = $correo;
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->Username = 'bp754509@gmail.com';
        $mail->Password = 'qkse ycth akvp iqpa';

        $mail->setFrom('bp754509@gmail.com', 'COMTER');
        $mail->addAddress($correo);
        $mail->Subject = 'Código de Verificación';
        //$mail->Body = "Tu código de verificación es: $codigo_verificacion\n\nEste código caduca una vez que lo ingreses en el siguiente formulario para completar tu registro.";
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->AddEmbeddedImage('../ico/comter.png', 'logo_comter'); 
        
        $mail->Body = "
        <html>
        <head>
          <style>
           
            body {
              background-color: #1b419b;
              margin: 0;
              padding: 0;
              height: 100%;
              display: flex;
              justify-content: center;
              align-items: center;
              text-align: center;
              color: white;
              font-family: Arial, sans-serif;
            }
        
          
            .contenido {
              display: flex;
              flex-direction: column;
              align-items: center;
              justify-content: center;
              text-align: center;
              color: white; 
              max-width: 600px; 
            }
        
            .codigo-container {
              margin-top: 30px;
            }
        
            .codigo {
              font-size: 48px;
              font-weight: bold;
              margin-top: 20px;
            }
        
            .imagen-container {
              margin-bottom: 20px;
            }
        
            img {
              width: 120px; 
              height: 120px; 
              object-fit: contain; 
            }
          </style>
        </head>
        <body style='background-color: #1b419b; margin: 0; padding: 0; height: 100%;'>
          <div class='contenido'>
            <div class='imagen-container'>
              <img src='cid:logo_comter' alt='Comter Logo'>
            </div>
            <div class='codigo-container'>
              <p>Tu código de verificación es:</p>
              <div class='codigo'>$codigo_verificacion</div>
              <p>Este código caduca una vez que lo ingreses en el formulario.</p>
            </div>
          </div>
        </body>
        </html>
        ";
        if (!$mail->send()) {
            echo json_encode(['status' => 'error', 'message' => 'Error al enviar el correo: ' . $mail->ErrorInfo]);
            exit();
        }

        echo json_encode(['status' => 'success', 'message' => 'Correo de verificación enviado correctamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar al usuario en la base de datos.']);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    exit();
}
?>
