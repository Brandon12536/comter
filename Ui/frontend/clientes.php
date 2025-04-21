<?php 
session_start();
require '../../config/connection.php';
require '../../phpmailer/src/Exception.php';
require '../../phpmailer/src/PHPMailer.php';
require '../../phpmailer/src/SMTP.php';

if (!isset($_SESSION['id_administrador'])) {
    echo 'La sesión no tiene el id_administrador.';
    exit();
}

$id_administrador = $_SESSION['id_administrador'];

$db = new Database();
$con = $db->conectar();
$con->exec("SET NAMES 'utf8'");

$sql = "SELECT nombre, apellido, compania, business_unit, telefono, correo, role, fecha_registro
        FROM administrador 
        WHERE id_administrador = :id_administrador";
$stmt = $con->prepare($sql);
$stmt->bindParam(':id_administrador', $id_administrador, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $nombre = $row['nombre'];
    $apellido = $row['apellido'];
    $compania = $row['compania'];
    $business_unit = $row['business_unit'];
    $telefono = $row['telefono'];
    $correo = $row['correo'];
    $role = $row['role'];
    $created_at = $row['fecha_registro'];

    $photo = '../../assets/img/avatars/1.png';
} else {
    echo 'Proveedor no encontrado o cuenta no válida.';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $campos_requeridos = [
            'compania' => 'Compañía',
            'business_unit' => 'Business Unit',
            'nombre' => 'Nombre',
            'apellido' => 'Apellido',
            'correo' => 'Correo',
            'telefono' => 'Teléfono'
        ];

        $campos_vacios = [];
        foreach ($campos_requeridos as $campo => $nombre) {
            if (empty($_POST[$campo])) {
                $campos_vacios[] = $nombre;
            }
        }

        if (!empty($campos_vacios)) {
            $_SESSION['mensaje'] = "Los siguientes campos son obligatorios: " . implode(", ", $campos_vacios);
            $_SESSION['tipo_mensaje'] = "error";
            header('Location: ../administrador.php');
            exit();
        }

        if (!filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['mensaje'] = "El formato del correo electrónico no es válido";
            $_SESSION['tipo_mensaje'] = "error";
            header('Location: ../administrador.php');
            exit();
        }

        if (strlen($_POST['telefono']) < 10) {
            $_SESSION['mensaje'] = "El número de teléfono debe tener al menos 10 dígitos";
            $_SESSION['tipo_mensaje'] = "error";
            header('Location: ../administrador.php');
            exit();
        }

        $sqlCheckEmail = "SELECT COUNT(*) FROM usuarios WHERE correo = :correo";
        $stmtCheckEmail = $con->prepare($sqlCheckEmail);
        $stmtCheckEmail->bindParam(':correo', $_POST['correo'], PDO::PARAM_STR);
        $stmtCheckEmail->execute();

        if ($stmtCheckEmail->fetchColumn() > 0) {
            $_SESSION['mensaje'] = "El correo electrónico ya está registrado";
            $_SESSION['tipo_mensaje'] = "error";
            header('Location: ../administrador.php');
            exit();
        }

        $codigo_verificacion = sprintf("%04d", rand(0, 9999));
        $password_sin_cifrar = $_POST['password'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $permiso_ver = isset($_POST['permiso_ver']) ? 1 : 0;
        $permiso_editar = isset($_POST['permiso_editar']) ? 1 : 0;
        $permiso_capturar = isset($_POST['permiso_capturar']) ? 1 : 0;

        $sqlUsuario = "INSERT INTO usuarios (
            compania, 
            business_unit, 
            nombre, 
            apellido, 
            correo, 
            telefono, 
            codigo_verificacion,
            role,
            verificado,
            password
        ) VALUES (
            :compania,
            :business_unit,
            :nombre,
            :apellido,
            :correo,
            :telefono,
            :codigo_verificacion,
            'Cliente',
            1,
            :password
        )";

        $stmtUsuario = $con->prepare($sqlUsuario);
        $stmtUsuario->bindParam(':compania', $_POST['compania'], PDO::PARAM_STR);
        $stmtUsuario->bindParam(':business_unit', $_POST['business_unit'], PDO::PARAM_STR);
        $stmtUsuario->bindParam(':nombre', $_POST['nombre'], PDO::PARAM_STR);
        $stmtUsuario->bindParam(':apellido', $_POST['apellido'], PDO::PARAM_STR);
        $stmtUsuario->bindParam(':correo', $_POST['correo'], PDO::PARAM_STR);
        $stmtUsuario->bindParam(':telefono', $_POST['telefono'], PDO::PARAM_STR);
        $stmtUsuario->bindParam(':codigo_verificacion', $codigo_verificacion, PDO::PARAM_STR);
        $stmtUsuario->bindParam(':password', $password, PDO::PARAM_STR);

        if ($stmtUsuario->execute()) {
            $id_usuario = $con->lastInsertId();

            $sqlRolPermiso = "INSERT INTO roles_permisos_usuarios (id_usuarios, permiso_ver, permiso_editar, permiso_capturar, asignado_por) 
                              VALUES (:id_usuarios, :permiso_ver, :permiso_editar, :permiso_capturar, :asignado_por)";
            $stmtRolPermiso = $con->prepare($sqlRolPermiso);
            $stmtRolPermiso->bindParam(':id_usuarios', $id_usuario, PDO::PARAM_INT);
            $stmtRolPermiso->bindParam(':permiso_ver', $permiso_ver, PDO::PARAM_INT);
            $stmtRolPermiso->bindParam(':permiso_editar', $permiso_editar, PDO::PARAM_INT);
            $stmtRolPermiso->bindParam(':permiso_capturar', $permiso_capturar, PDO::PARAM_INT);
            $stmtRolPermiso->bindParam(':asignado_por', $id_administrador, PDO::PARAM_INT);

            if ($stmtRolPermiso->execute()) {
                $id_rol_permiso_usuarios = $con->lastInsertId();

                $mail = new PHPMailer\PHPMailer\PHPMailer();
                $mail->isSMTP();
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'tls';
                $mail->Host = 'smtp.gmail.com';
                $mail->Port = 587;
                $mail->Username = 'bp754509@gmail.com';
                $mail->Password = 'qkse ycth akvp iqpa';

                $mail->setFrom('bp754509@gmail.com', 'COMTER');
                $mail->addAddress($_POST['correo']);
                $mail->Subject = 'Credenciales de Acceso - COMTER';
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->AddEmbeddedImage('../../ico/comter.png', 'logo_comter');

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
                            color: white !important;
                            font-family: Arial, sans-serif;
                        }
                        .contenido {
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            text-align: center;
                            color: white !important; 
                            max-width: 600px; 
                        }
                        .credenciales {
                            margin-top: 30px;
                            background-color: rgba(255, 255, 255, 0.1);
                            padding: 20px;
                            border-radius: 10px;
                        }
                        .credenciales h2, 
                        .credenciales p, 
                        .credenciales strong,
                        .credenciales span {
                            color: white !important;
                        }
                        .imagen-container {
                            margin-bottom: 20px;
                        }
                        img {
                            width: 120px; 
                            height: 120px; 
                            object-fit: contain; 
                        }
                        * {
                            color: white !important;
                        }
                        a {
                            color: white !important;
                            text-decoration: none !important;
                        }
                    </style>
                </head>
                <body style='background-color: #1b419b; margin: 0; padding: 0; height: 100%; color: white !important;'>
                    <div class='contenido'>
                        <div class='imagen-container'>
                            <img src='cid:logo_comter' alt='Comter Logo'>
                        </div>
                        <div class='credenciales'>
                            <h2 style='color: white !important;'>¡Bienvenido a COMTER!</h2>
                            <p style='color: white !important;'>Se ha creado una cuenta para ti con las siguientes credenciales:</p>
                            <p style='color: white !important;'>
                                <strong style='color: white !important;'>Correo electrónico:</strong> 
                                <span style='color: white !important;'>{$_POST['correo']}</span>
                            </p>
                            <p style='color: white !important;'>
                                <strong style='color: white !important;'>Contraseña:</strong> 
                                <span style='color: white !important;'>{$password_sin_cifrar}</span>
                            </p>
                        </div>
                        <p style='margin-top: 20px; color: white !important;'>Por favor, guarda esta información en un lugar seguro.</p>
                    </div>
                </body>
                </html>";
            
                        $mail->IsHTML(true);
                        $mail->AltBody = "
            ¡Bienvenido a COMTER!
            Se ha creado una cuenta para ti con las siguientes credenciales:
            Correo electrónico: {$_POST['correo']}
            Contraseña: {$password_sin_cifrar}
            Por favor, guarda esta información en un lugar seguro.";

                if ($mail->send()) {
                    $_SESSION['mensaje'] = "Usuario registrado y correo enviado correctamente";
                    $_SESSION['tipo_mensaje'] = "success";
                    header('Location: ../administrador.php');
                    exit();
                } else {
                    $_SESSION['mensaje'] = "No se pudo enviar el correo";
                    $_SESSION['tipo_mensaje'] = "error";
                    header('Location: ../administrador.php');
                    exit();
                }
            } else {
                $_SESSION['mensaje'] = "No se pudo asignar permisos al usuario";
                $_SESSION['tipo_mensaje'] = "error";
                header('Location: ../administrador.php');
                exit();
            }
        } else {
            $_SESSION['mensaje'] = "No se pudo registrar al usuario";
            $_SESSION['tipo_mensaje'] = "error";
            header('Location: ../administrador.php');
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['mensaje'] = "Error: " . $e->getMessage();
        $_SESSION['tipo_mensaje'] = "error";
        header('Location: ../administrador.php');
        exit();
    }
}
?>





<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../ico/comter.png" type="image/x-icon">
    <link rel="stylesheet" href="../../css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="../../css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="../../css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="../../css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="../../css/styles_administrador.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />


    <title>Comter</title>

</head>

<body>



    <header class="header fixed-top" style="background-color:#1B419B;">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo d-flex align-items-center">
                        <a href="../administrador.php"><img src="../../ico/comter.png" alt="" style="width:50px"></a>
                        <button class="navbar-toggler d-lg-none ms-auto" type="button" data-bs-toggle="collapse"
                            data-bs-target="#sidebarMenu">
                            <i class="fas fa-bars text-white"></i>
                        </button>
                    </div>
                </div>
                <div class="col-lg-10">
                    <div class="header__nav__option">
                        <nav class="header__nav__menu mobile-menu">
                            <ul>
                                <!--<li class="active"><a href="administrador.php">Home</a></li>-->
                                <!--<li>
                                    <select class="form-select transparent-select"
                                        onchange="window.location.href=this.value">
                                        <option value="#" selected disabled>Seleccione una opción</option>
                                        <option value="cliente_panel.php">PCBA</option>
                                        <option value="materiales.php">Materiales Acumulados o de Almacén</option>
                                        <option value="sem42.php">MOLEX SEM42</option>
                                        <option value="sem43.php">MOLEX SEM43</option>
                                        <option value="sem44.php">MOLEX SEM44</option>
                                        <option value="sem45.php">MOLEX SEM45</option>
                                        <option value="sem46.php">MOLEX SEM46</option>
                                        <option value="sem47.php">MOLEX SEM47</option>
                                        <option value="sem48.php">MOLEX SEM48</option>
                                        <option value="sem49.php">MOLEX SEM49</option>
                                        <option value="sem50.php">MOLEX SEM50</option>
                                        <option value="sem51.php">MOLEX SEM51</option>
                                        <option value="sem52.php">MOLEX SEM52</option>
                                    </select>
                                </li>-->
                                <small class="text-muted" style="text-transform:uppercase;"><span
                                        style="color: #fff; font-weight: bold;">Bienvenido</span>
                                    <span
                                        style="color: #fff; font-weight: bold;"><?php echo $role; ?></span></small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <li>



                                    <a href="#" class="no-decoration">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $photo; ?>" alt="" class="user-image" />
                                            <span
                                                class="fw-semibold d-block ms-2 no-decoration"><?php echo $nombre . ' ' . $apellido; ?></span>
                                        </div>
                                    </a>
                                    <ul class="dropdown">

                                        <li> <a href="#" class="no-decoration"> <small class="text-muted">Rol:
                                                    <?php echo $role; ?></small></a>
                                            <hr>

                                        <li><a href="#" class="no-decoration" onclick="confirmLogout()">Cerrar
                                                sesión</a></li>
                                    </ul>
                                </li>

                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>
    <br><br>

    <div class="container-fluid">
        <div class="row">

            <div class="col-lg-2 sidebar collapse d-lg-block" id="sidebarMenu">
                <div class="d-flex justify-content-between align-items-center px-3 mb-3">
                    <div class="d-lg-none text-center pt-3">
                        <a href="../administrador.php">
                            <img src="../../ico/comter.png" alt="Comter" style="width:50px; z-index: 1031;"
                                class="img-fluid">
                        </a>
                    </div>
                    <button id="toggleSidebar" class="btn d-none d-lg-block">
                        <i class="fas fa-bars text-white"></i>
                    </button>
                </div>
                <div class="sidebar-menu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="graficas.php">
                            <i class="fas fa-chart-bar"></i> Gráficas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="respaldos.php">
                                <i class="fas fa-database"></i> Respaldos
                            </a>
                        </li>

                        <li class="nav-item mt-1">
                        <button type="button" class="btn btn-primary nav-link" data-bs-toggle="modal" 
                                data-bs-target="#nuevoModal">
                                <i class="fas fa-plus"></i> Registro Comter
                            </button>
                        </li>

                       
<li class="nav-item mt-1">
    <button class="btn btn-success" onclick="mostrarSweetAlert()">
        <i class="fas fa-file-excel"></i> Exportar a Excel
    </button>
</li>

<li class="nav-item mt-1">
                            <a class="nav-link" href="clientes.php">
                                <i class="fas fa-users"></i> Clientes
                            </a>
                        </li>
                        <li class="nav-item mt-1">
                                    <button type="button" class="btn btn-primary nav-link" data-bs-toggle="modal" 
                                            data-bs-target="#modalCliente">
                                            <i class="fas fa-plus"></i> Registro Cliente
                                        </button>
                                    </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-10 main-content">
                <div class="container-fluid px-4">
                    <div class="row">
                        <div class="col-12 text-center mb-4">
                         
                        </div>

                        <?php

                        $sqlCount = "SELECT COUNT(*) FROM usuarios";
                        $stmtCount = $con->prepare($sqlCount);
                        $stmtCount->execute();
                        $rowCount = $stmtCount->fetchColumn();
                        ?>

                        <div class="col-12 mb-4 d-flex justify-content-between">
                           
                        </div>

                        <script>
 
    function generarContraseña(longitud) {
        const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
        let contraseña = '';
        for (let i = 0; i < longitud; i++) {
            const randomIndex = Math.floor(Math.random() * caracteres.length);
            contraseña += caracteres.charAt(randomIndex);
        }
        return contraseña;
    }

  
    function sugerirContraseñaCliente() {
        const contraseñaSugerida = generarContraseña(12);
        document.getElementById('password_cliente').value = contraseñaSugerida;
    }

   
    const togglePasswordCliente = document.querySelector("#togglePasswordCliente");
    const passwordFieldCliente = document.querySelector("#password_cliente");
    const eyeIconCliente = document.querySelector("#eyeIconCliente");

    togglePasswordCliente.addEventListener("click", function () {
        if (passwordFieldCliente.type === "password") {
            passwordFieldCliente.type = "text";
            eyeIconCliente.classList.remove("fa-eye");
            eyeIconCliente.classList.add("fa-eye-slash");
        } else {
            passwordFieldCliente.type = "password";
            eyeIconCliente.classList.remove("fa-eye-slash");
            eyeIconCliente.classList.add("fa-eye");
        }
    });
</script>

<div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="nuevoModalLabelCliente" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nuevoModalLabelCliente">Registro de Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevoRegistroCliente" method="POST" action="clientes.php">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Compañía</label>
                            <input type="text" class="form-control" name="compania" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Business Unit</label>
                            <input type="text" class="form-control" name="business_unit" required>
                        </div>
                       
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Apellido</label>
                            <input type="text" class="form-control" name="apellido" required>
                        </div>
                       
                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="correo" required>
                        </div>
                       
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" name="telefono" required>
                        </div>
                       
                        <div class="col-md-6">
                            <label class="form-label">Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" id="password_cliente" required>
                                <span class="input-group-text" id="togglePasswordCliente">
                                    <i class="fas fa-eye" id="eyeIconCliente"></i>
                                </span>
                            </div>
                        </div>
                    
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary" onclick="sugerirContraseñaCliente()">
                                <i class="fas fa-key"></i> Sugerir Contraseña
                            </button>
                        </div>
                       
                        <div class="row mb-3">
                            <div class="col-12">
                                <h4>Permisos</h4>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permiso_ver" id="permiso_ver">
                                    <label class="form-check-label" for="permiso_ver">Permiso para ver</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permiso_editar" id="permiso_editar">
                                    <label class="form-check-label" for="permiso_editar">Permiso para editar</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permiso_capturar" id="permiso_capturar">
                                    <label class="form-check-label" for="permiso_capturar">Permiso para capturar</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="guardarBtnCliente" disabled>Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</div>
<script>
    const form = document.getElementById('formNuevoRegistroCliente');
    const guardarBtn = document.getElementById('guardarBtnCliente');

    function checkFormCompletion() {
       
        const inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], input[type="password"], select');
        let allFilled = true;

        inputs.forEach(input => {
           
            if (input.value.trim() === '') {
                allFilled = false;
            }
        });

     
        guardarBtn.disabled = !allFilled;

        console.log('Estado del botón de guardar: ', guardarBtn.disabled);
    }

 
    form.addEventListener('input', checkFormCompletion);
    form.addEventListener('change', checkFormCompletion);

    
    checkFormCompletion();
</script>



<div class="modal fade" id="editarModalCliente" tabindex="-1" aria-labelledby="editarModalLabelCliente" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarModalLabelCliente">Editar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarClientes" method="POST">
                    <input type="hidden" id="edit_id_usuarios" name="id_usuarios">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_compania" class="form-label">Compañía</label>
                            <input type="text" class="form-control" id="edit_compania" name="compania" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_business_unit" class="form-label">Business Unit</label>
                            <input type="text" class="form-control" id="edit_business_unit" name="business_unit" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="edit_apellido" name="apellido" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_correo" class="form-label">Correo</label>
                            <input type="email" class="form-control" id="edit_correo" name="correo" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="edit_telefono" name="telefono" required>
                        </div>
                       
                        <div class="col-12 mt-4">
                            <h6 class="mb-3">Permisos</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="edit_permiso_ver" name="permiso_ver">
                                        <label class="form-check-label" for="edit_permiso_ver">Permiso para ver</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="edit_permiso_editar" name="permiso_editar">
                                        <label class="form-check-label" for="edit_permiso_editar">Permiso para editar</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="edit_permiso_capturar" name="permiso_capturar">
                                        <label class="form-check-label" for="edit_permiso_capturar">Permiso para capturar</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="cerrarModalEditar()">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>

    const editarModal = document.getElementById('editarModalCliente');
    editarModal.addEventListener('hidden.bs.modal', function () {
        location.reload();
    });
</script>
<?php

$sql = "
SELECT 
    u.id_usuarios,
    u.compania,
    u.business_unit,
    u.nombre,
    u.apellido,
    u.correo,
    u.telefono,
    u.created_at AS fecha_alta
FROM 
    usuarios u
";
$stmt = $con->prepare($sql);
$stmt->execute();
?>

<div class="col-12">
<?php if (isset($_SESSION['mensaje_cliente']) && isset($_SESSION['tipo_mensaje_cliente'])): ?>
    <div class="alert alert-<?php echo $_SESSION['tipo_mensaje_cliente'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show mt-3" role="alert">
        <strong><?php echo $_SESSION['tipo_mensaje_cliente'] === 'success' ? '¡Éxito!' : 'Error'; ?>:</strong>
        <?php echo $_SESSION['mensaje_cliente']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php
    unset($_SESSION['mensaje_cliente']);
    unset($_SESSION['tipo_mensaje_cliente']);
endif;
?>

    <div class="table-responsive">
        <div class="table-responsive" style="max-height: 570px; overflow-y: auto;">
            <table class="table table-bordered table-hover">
                <thead class="table-primary" style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th style="background-color:#79f873;">NO.</th>
                        <th style="background-color:#79f873;">COMPAÑÍA</th>
                        <th>BUSINESS UNIT</th>
                        <th>NOMBRE</th>
                        <th>APELLIDO</th>
                        <th>CORREO ELECTRONICO</th>
                        <th style="background-color:#cfcfcf;">TELEFONO PROPIO O DE CONTACTO</th>
                        <th style="background-color:#79f873;">FECHA ALTA</th>
                        <th style="background-color:#f2d7d5;">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($stmt->rowCount() > 0): ?>
                        <?php 
                        $counter = 1;
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $counter++; ?></td>
                                <td><?php echo htmlspecialchars($row['compania']); ?></td>
                                <td><?php echo htmlspecialchars($row['business_unit']); ?></td>
                                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                                <td><?php echo htmlspecialchars($row['correo']); ?></td>
                                <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                                <td>
                                    <?php
                                        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'spanish');
                                        mb_internal_encoding("UTF-8");
                                        $fecha = strtotime($row['fecha_alta']);
                                        $fecha_formateada = strftime("%A %d %B %Y", $fecha);
                                        echo utf8_encode(ucwords($fecha_formateada));
                                    ?>
                                </td>
                                <td>
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editarModalCliente" onclick="cargarDatos('<?php echo $row['id_usuarios']; ?>')">
    <i class="fas fa-edit"></i>
</button>

                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?php echo $row['id_usuarios']; ?>)">
    <i class="fas fa-trash-alt"></i>
</a>

                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">No hay información para mostrar</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>




                        <style>
                            .table-responsive {
                                overflow-x: auto;
                            }


                            @media (max-width: 768px) {
                                .table thead {
                                    display: none;
                                }

                                .table,
                                .table tbody,
                                .table tr,
                                .table td {
                                    display: block;
                                    width: 100%;
                                }

                                .table td {
                                    text-align: right;
                                    position: relative;
                                    padding-left: 50%;
                                }

                                .table td::before {
                                    content: attr(data-label);
                                    position: absolute;
                                    left: 10px;
                                    top: 10px;
                                    font-weight: bold;
                                }

                                .table td {
                                    padding-left: 10px;
                                }
                            }


                            @media (min-width: 769px) {
                                .table {
                                    display: table;
                                }

                                .table td {
                                    text-align: left;
                                }
                            }
                        </style>


                    </div>
                </div>
            </div>

        </div>
    </div>


    </div>
    </div>
    </div>
    </div>
    </div>




    <div class="modal fade" id="nuevoModal" tabindex="-1" aria-labelledby="nuevoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nuevoModalLabel">Registro Comter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevoRegistro" method="POST" action="../administrador.php">
                

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Compañía</label>
                            <input type="text" class="form-control" name="compania">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Business Unit</label>
                            <input type="text" class="form-control" name="business_unit">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellido</label>
                            <input type="text" class="form-control" name="apellido">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="correo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" name="telefono">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Departamento</label>
                            <select class="form-select" name="departamento">
                                <option value="">Seleccione un departamento</option>
                                <option value="ADMINISTRACION">ADMINISTRACION</option>
                                <option value="PRODUCCION">PRODUCCION</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Puesto</label>
                            <select class="form-select" name="puesto">
                                <option value="">Seleccione un puesto</option>
                                <option value="GERENTE GENERAL">GERENTE GENERAL</option>
                                <option value="SUPERVISOR">SUPERVISOR</option>
                                <option value="PRODUCCION">PRODUCCION</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Turno</label>
                            <select class="form-select" name="turno_completo">
                                <option value="">Seleccione un turno</option>
                                <option value="1er.">1er.</option>
                                <option value="2do.">2do.</option>
                                <option value="3er.">3er.</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" id="password">
                                <span class="input-group-text" id="togglePassword">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary" onclick="sugerirContraseña()">
                                <i class="fas fa-key"></i> Sugerir Contraseña
                            </button>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hora de Inicio</label>
                            <input type="time" class="form-control" name="hora_inicio">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hora de Fin</label>
                            <input type="time" class="form-control" name="hora_fin">
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <h4>Permisos</h4>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permiso_ver" id="permiso_ver">
                                    <label class="form-check-label" for="permiso_ver">Permiso para ver</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permiso_editar" id="permiso_editar">
                                    <label class="form-check-label" for="permiso_editar">Permiso para editar</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permiso_capturar" id="permiso_capturar">
                                    <label class="form-check-label" for="permiso_capturar">Permiso para capturar</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="cerrarModalNuevo()">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="guardarBtn">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    <footer class="content-footer footer" style="background-color:#edebea">
        <div class="container-xxl d-flex flex-wrap justify-content-center py-2 flex-md-row flex-column text-center"
            style="color:#838383;">
            <div class="mb-2 mb-md-0 fw-bolder">
                ©
                <script>
                    document.write(new Date().getFullYear());
                </script>
                , Comter |
                <a href="#" class="footer-link fw-bolder no-decoration" style="color: #6c757d;">Osdems Digital Group</a>
            </div>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmLogout() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Quieres cerrar la sesión?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../../admin/backend/home/logout.php';
                }
            });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            const toggleBtn = document.getElementById('toggleSidebar');


            if (window.innerWidth >= 992) {
                toggleBtn.addEventListener('click', function () {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');


                    const isCollapsed = sidebar.classList.contains('collapsed');
                    localStorage.setItem('sidebarCollapsed', isCollapsed);
                });


                const savedState = localStorage.getItem('sidebarCollapsed');
                if (savedState === 'true') {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                }
            }
        });
    </script>
    <script>

        function generarContraseña(longitud) {
            const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
            let contraseña = '';
            for (let i = 0; i < longitud; i++) {
                const randomIndex = Math.floor(Math.random() * caracteres.length);
                contraseña += caracteres.charAt(randomIndex);
            }
            return contraseña;
        }


        function sugerirContraseña() {
            const contraseñaSugerida = generarContraseña(12);
            document.getElementById('password').value = contraseñaSugerida;
        }


        const togglePassword = document.querySelector("#togglePassword");
        const passwordField = document.querySelector("#password");
        const eyeIcon = document.querySelector("#eyeIcon");

        togglePassword.addEventListener("click", function () {

            if (passwordField.type === "password") {
                passwordField.type = "text";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            }
        });
    </script>



<script>
   
    function cargarDatos(id_usuarios) {
        Swal.fire({
            title: 'Cargando...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        
        fetch(`../backend/obtener_registro_usuario.php?id_usuarios=${id_usuarios}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }

               
                document.getElementById('edit_id_usuarios').value = data.id_usuarios;
                document.getElementById('edit_compania').value = data.compania;
                document.getElementById('edit_business_unit').value = data.business_unit;
                document.getElementById('edit_nombre').value = data.nombre;
                document.getElementById('edit_apellido').value = data.apellido;
                document.getElementById('edit_correo').value = data.correo;
                document.getElementById('edit_telefono').value = data.telefono;

               
                document.getElementById('edit_permiso_ver').checked = Boolean(data.permiso_ver);
                document.getElementById('edit_permiso_editar').checked = Boolean(data.permiso_editar);
                document.getElementById('edit_permiso_capturar').checked = Boolean(data.permiso_capturar);

                Swal.close();

              
                const modal = new bootstrap.Modal(document.getElementById('editarModalCliente'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Error al cargar los datos. Por favor, intente nuevamente.'
                });
            });
    }

   
    document.getElementById('formEditarClientes').addEventListener('submit', function (e) {
    e.preventDefault();

   
    const id_usuarios = document.getElementById('edit_id_usuarios').value;
    if (!id_usuarios) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se ha proporcionado un ID de usuario',
        });
        return;
    }

    Swal.fire({
        title: '¿Estás seguro?',
        text: "¿Deseas guardar los cambios?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData(this);

            
            formData.append('id_usuarios', id_usuarios);

            fetch('../backend/actualizar_registro_usuario.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }

                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Los cambios se guardaron correctamente',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload();
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Error al guardar los cambios'
                });
            });
        }
    });
});


  
   
</script>
    <script>
        function cerrarModalEditar() {
            const modal = document.getElementById('editarModalCliente');
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
                document.getElementById('formEditarClientes').reset();
            }
        }

        function cerrarModalNuevo() {
            const modal = document.getElementById('nuevoModal');
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
                document.getElementById('formNuevoRegistro').reset();
            }
        }


        document.getElementById('editarModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('formEditar').reset();
        });

        document.getElementById('nuevoModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('formNuevoRegistro').reset();
        });
    </script>
<script>
  function confirmarEliminacion(id_usuarios) {

    fetch(`../backend/eliminar_registro_cliente.php?id_usuarios=${id_usuarios}`)
        .then(response => response.json())
        .then(data => {
            let mensaje = '';
            
          
            if (data.warning) {
                let usuarioInfo = `
                    <strong>Datos del Usuario a Eliminar:</strong><br>
                    Compañía: ${data.usuario_data.compania}<br>
                    Unidad de Negocio: ${data.usuario_data.business_unit}<br>
                    Nombre: ${data.usuario_data.nombre} ${data.usuario_data.apellido}<br>
                    Teléfono: ${data.usuario_data.telefono}<br>
                    Correo: ${data.usuario_data.correo}<br>
                    Rol: ${data.usuario_data.role}<br>
                    Fecha de Creación: ${formatearFecha(data.usuario_data.created_at)}<br>
                `;
                mensaje = data.message.replace(/\n/g, '<br>') + "<br><br>" + usuarioInfo;
            } else {
                mensaje = 'Este usuario no tiene registros asociados en ninguna tabla.';
            }

            Swal.fire({
                title: '¿Estás seguro de eliminar este registro?',
                html: mensaje,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'swal-wide',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                
                    fetch(`../backend/eliminar_registro_cliente.php?id_usuarios=${id_usuarios}&confirmar=1`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: '¡Eliminado!',
                                    text: data.message,
                                    icon: 'success'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: data.error || 'Hubo un error al eliminar los registros.',
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error',
                                text: 'Hubo un error al procesar la solicitud.',
                                icon: 'error'
                            });
                        });
                }
            });
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: 'Hubo un error al procesar la solicitud.',
                icon: 'error'
            });
        });
  }

 
  function formatearFecha(fecha) {
    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const date = new Date(fecha);
    return date.toLocaleDateString('es-ES', opciones);
  }
</script>



    <style>
        .swal-wide {
            width: 600px !important;
        }
    </style>
    <script>
function mostrarSweetAlert() {
    Swal.fire({
        title: 'Seleccionar Rango de Fechas y Tipo de Exportación',
        html: `
            <label for="fechaInicio">Fecha Inicio:</label>
            <input type="date" id="fechaInicio" class="swal2-input">

            <label for="fechaFin">Fecha Fin:</label>
            <input type="date" id="fechaFin" class="swal2-input">

            <label for="tipoExportacion">Seleccionar Exportación:</label>
            <select id="tipoExportacion" class="swal2-select" style="width: 70%; padding: 8px;">
                <option value="" disabled selected>Seleccione una opción</option>
                <option value="../backend/exportar_excel_pcba.php">Exportar PCBA</option>
                <option value="../backend/exportar_excel_materiales.php">Exportar Materiales</option>
                <option value="../backend/exportar_excel_molex_42.php">Exportar Molex SEM 42</option>
                <option value="../backend/exportar_excel_molex_43.php">Exportar Molex SEM 43</option>
                <option value="../backend/exportar_excel_molex_44.php">Exportar Molex SEM 44</option>
                <option value="../backend/exportar_excel_molex_45.php">Exportar Molex SEM 45</option>
                <option value="../backend/exportar_excel_molex_46.php">Exportar Molex SEM 46</option>
                <option value="../backend/exportar_excel_molex_47.php">Exportar Molex SEM 47</option>
                <option value="../backend/exportar_excel_molex_48.php">Exportar Molex SEM 48</option>
                <option value="../backend/exportar_excel_molex_49.php">Exportar Molex SEM 49</option>
                <option value="../backend/exportar_excel_molex_50.php">Exportar Molex SEM 50</option>
                <option value="../backend/exportar_excel_molex_51.php">Exportar Molex SEM 51</option>
                <option value="../backend/exportar_excel_molex_52.php">Exportar Molex SEM 52</option>
            </select>
        `,
        showCancelButton: true,
        confirmButtonText: 'Exportar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            let fechaInicio = document.getElementById("fechaInicio").value;
            let fechaFin = document.getElementById("fechaFin").value;
            let tipoExportacion = document.getElementById("tipoExportacion").value;

            if (!fechaInicio || !fechaFin || !tipoExportacion) {
                Swal.showValidationMessage("Por favor, selecciona un rango de fechas y un tipo de exportación.");
                return false;
            }

            Swal.fire({
                title: 'Validando...',
                text: 'Por favor, espera mientras validamos los datos.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            return fetch(`${tipoExportacion}?fechaInicio=${encodeURIComponent(fechaInicio)}&fechaFin=${encodeURIComponent(fechaFin)}&validar=1`)
    .then(response => response.json())
    .then(data => {
        if (data.status === "error") {
            throw new Error(data.message);
        }

        Swal.close();
        window.location.href = `${tipoExportacion}?fechaInicio=${encodeURIComponent(fechaInicio)}&fechaFin=${encodeURIComponent(fechaFin)}`;
    })
    .catch(error => {
        Swal.fire({
            title: 'Error',
            text: error.message || "Ocurrió un error inesperado.",
            icon: 'error'
        });
    });

        }
    });
}

    </script>

</body>

</html>