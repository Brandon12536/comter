<?php
session_start();

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../ico/comter.png" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/8.1.0/mdb.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="../css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="../css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="../css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="../css/style.css" type="text/css">
    <link rel="stylesheet" href="../css/styles_login.css">
    <style>
        ::-webkit-scrollbar {
            width: 12px;
            height: 12px;
            transition: all 0.3s ease;
        }

        ::-webkit-scrollbar-thumb {
            background-color: rgb(27, 114, 155);
            border-radius: 10px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            margin: 4px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }


        ::-webkit-scrollbar-thumb:hover {
            background-color: rgb(27, 114, 155);
            transform: scale(1.2);
        }


        ::-webkit-scrollbar-track {
            background-color: #f1f1f1;
            transition: background-color 0.3s ease;
            border-radius: 10px;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1);
        }


        ::-webkit-scrollbar-track:hover {
            background-color: #e0e0e0;
        }


        html {
            scrollbar-width: thin;
            scrollbar-color: rgb(27, 114, 155)B #f1f1f1;
            transition: scrollbar-color 0.3s ease;
        }


        html:hover {
            scrollbar-color: rgb(27, 114, 155) #e0e0e0;
        }
    </style>
    <title>Comter</title>

</head>

<body>

    
<header class="header fixed-top" style="background-color:#1B419B;">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo">
                        <a href="../index.php"><img src="../ico/comter.png" alt="" style="width:50px"></a>
                    </div>
                </div>
                <div class="col-lg-10">
                    <div class="header__nav__option">
                        <nav class="header__nav__menu mobile-menu">
                            <ul>
                                <!--<li class="active"><a href="../index.php">Home</a></li>-->
                                <li><a href="login.php">Cliente</a></li>
                                <li><a href="login_proveedor.php">Proveedor</a></li>
                                <li><a href="admin.php">Administrador</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>

    <br><br><br><br><br>

    <div class="form-container">
    <form class="form" id="login-form" method="POST">

            <p class="title"
                style="display: flex; align-items: center; justify-content: flex-start; margin-left: -15px;">
                <img src="../ico/comter.png" alt="" style="width:40px; height:auto; margin-right: 10px;">
                Iniciar sesión
            </p>

            <p class="message text-center">Accede a tu cuenta para continuar.</p>

            <br>

            <label for="email" style="display: block; margin-bottom: 10px;">
                <input class="input" type="email" id="email" name="email" placeholder="Correo electrónico" required="">
                <span style="display: none;">Correo electrónico</span>
            </label>

            <label for="password" style="display: block; margin-bottom: 10px;">
                <input class="input" type="password" id="password" name="password" placeholder="Contraseña" required="">
                <span style="display: none;">Contraseña</span>
            </label>

            <div class="form-check" style="margin-bottom: 15px;">
                <input class="form-check-input" type="checkbox" id="recordar" name="recordar">
                <label class="form-check-label" for="recordar" style="color: #000;">Recordar mis credenciales</label>
            </div>

            <button class="submit" type="submit" style="width: 100%; padding: 10px;">Iniciar sesión</button>

            <p class="signin" style="text-align: center; margin-top: 10px;">¿No tienes una cuenta? <a
                    href="registro.php">Regístrate aquí</a></p>
        </form>
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
                <a href="#" class="footer-link fw-bolder" style="color: #6c757d;">Osdems Digital Group</a>
            </div>
        </div>
    </footer>



    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/8.1.0/mdb.umd.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
   $(document).ready(function() {
   
    if(localStorage.getItem('email')) {
        $('#email').val(localStorage.getItem('email'));
        $('#password').val(localStorage.getItem('password'));
        $('#recordar').prop('checked', true);
    }

    $('#login-form').on('submit', function(e) {
        e.preventDefault();

       
        if($('#recordar').is(':checked')) {
            localStorage.setItem('email', $('#email').val());
            localStorage.setItem('password', $('#password').val());
        } else {
            localStorage.removeItem('email');
            localStorage.removeItem('password');
        }
        
        $.ajax({
            url: '../backend/login.php',  
            type: 'POST',                 
            dataType: 'json',             
            data: $(this).serialize(),    
            success: function(response) {
                if (response.status === 'success') {
                   
                    Swal.fire({
                        icon: 'success',
                        title: '¡Bienvenido!',
                        text: 'Inicio de sesión exitoso.',
                    }).then(() => {
                        window.location.href = 'cliente_panel.php'; 
                    });
                } else {
                  
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText); 
                console.log(status);            
                console.log(error);            
                Swal.fire({
                    icon: 'error',
                    title: '¡Oops!',
                    text: 'Hubo un problema con la conexión. Intenta nuevamente.',
                });
            }
        });
    });
});


  </script>

</body>

</html>