<?php
require '../config/connection.php';
$db = new Database();
$con = $db->conectar();

if (isset($_GET['token'])) {
    $activationToken = $_GET['token'];

    // Consulta para verificar si el token de activación es válido
    $sql = "SELECT * FROM usuarios WHERE activation_token = :token";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':token', $activationToken, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Activación de cuenta</title>
        <style>
            body {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                background-color: #1b2455;
                color: white;
                font-family: Arial, sans-serif;
                text-align: center;
            }
            .message {
                margin: 0;
            }
            .spinner {
                border: 8px solid rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                border-top: 8px solid #ffffff;
                width: 50px;
                height: 50px;
                animation: spin 1s linear infinite;
                margin: 20px auto;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            p {
                margin: 0;
                font-size: 16px;
            }
        </style>
    </head>
    <body>';

    if ($result) {
        // Si el token es válido, actualizamos el estado del usuario a activo
        $updateSql = "UPDATE usuarios SET active = 1, activation_token = NULL WHERE activation_token = :token";
        $updateStmt = $con->prepare($updateSql);
        $updateStmt->bindParam(':token', $activationToken, PDO::PARAM_STR);
        $updateStmt->execute();

        echo '<div class="message">
                <div class="spinner"></div>
                <p>Tu cuenta ha sido activada correctamente. Puedes iniciar sesión ahora.</p>
            </div>';

        header('refresh:2;url=../index.php');  // Redirige después de 2 segundos
        exit;
    } else {
        // Si no se encuentra el token en la base de datos
        echo '<div class="message">
                <p>Token de activación no válido.</p>
            </div>';
    }
} else {
    // Si no se proporciona un token
    echo '<div class="message">
            <p>Token de activación no proporcionado.</p>
        </div>';
}
echo '</body></html>';
?>
