<?php
session_start();
if (!isset($_SESSION['correo'])) {
    echo json_encode(['status' => 'error', 'message' => 'No se encontró un correo electrónico.']);
    exit();
}

$correo = $_SESSION['correo'];
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
    <title>Verificación de Código</title>
    <link rel="stylesheet" href="styles.css">
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
                                <li><a href="registro.php">Cliente</a></li>
                                <li><a href="login_proveedor.php">Proveedor</a></li>

                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>

    <br><br>
    <div class="verify-container">
        <form class="verify-form">
            <p class="heading">Verificación de Código</p>

            <div class="icon-container">
                <i class="fas fa-shield-alt icon"></i>
            </div>
            <p class="text-center" style="color:green;">Por favor, ingresa el código que hemos enviado a tu correo
                electrónico:</p>

            <div class="box">
                <input class="input" type="text" id="codigo_verificacion_1" maxlength="1" required>
                <input class="input" type="text" id="codigo_verificacion_2" maxlength="1" required>
                <input class="input" type="text" id="codigo_verificacion_3" maxlength="1" required>
                <input class="input" type="text" id="codigo_verificacion_4" maxlength="1" required>
            </div>
            <button type="button" class="btn1" id="submit-verification">Verificar Código</button>
        </form>
    </div>


    <br><br><br><br><br>

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
    <style>
        .verify-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }


        .verify-form {
            display: flex;
            flex-direction: column;
            width: 290px;
            height: 470px;
            border-radius: 15px;
            background-color: white;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transition: .4s ease-in-out;
        }

        .verify-form:hover {
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
            transform: scale(0.99);
        }


        .heading {
            text-align: center;
            color: black;
            font-weight: bold;
            font-size: 1.3em;
            margin-bottom: 10px;
        }


        .icon-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1em;
        }

        .icon {
            font-size: 3em;
            color: #000000;
        }


        .box {
            display: flex;
            justify-content: space-between;
            margin-top: 2em;
            margin-bottom: 2em;
        }

        .input {
            width: 2.2em;
            height: 2.2em;
            margin: 0.3em;
            border-radius: 5px;
            border: none;
            outline: none;
            background-color: rgb(235, 235, 235);
            box-shadow: inset 3px 3px 6px #d1d1d1, inset -3px -3px 6px #ffffff;
            text-align: center;
            font-size: 1.2em;
            transition: .4s ease-in-out;
        }

        .input:hover,
        .input:focus {
            background-color: lightgrey;
            box-shadow: inset 0px 0px 0px #d1d1d1, inset 0px 0px 0px #ffffff;
        }


        .btn1 {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 2em;
            transition: .4s ease-in-out;
            box-shadow: 1px 1px 3px #b5b5b5, -1px -1px 3px #ffffff;
        }

        .btn1:hover {
            background-color: #0056b3;
        }

        .btn1:active {
            box-shadow: inset 3px 3px 6px #b5b5b5, inset -3px -3px 6px #ffffff;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
           
            $('.input').on('input', function() {
                if (this.value.length === 1) {
                    $(this).next('.input').focus();
                }
            });

          
            $('.input').on('keydown', function(e) {
                if (e.key === 'Backspace' && this.value.length === 0) {
                    $(this).prev('.input').focus();
                }
            });

          
            $('.input').on('keypress', function(e) {
                if (e.keyCode < 48 || e.keyCode > 57) {
                    e.preventDefault();
                }
            });
        });

        $('#submit-verification').click(function () {
            var codigo_ingresado = $('#codigo_verificacion_1').val() +
                $('#codigo_verificacion_2').val() +
                $('#codigo_verificacion_3').val() +
                $('#codigo_verificacion_4').val();

            if (codigo_ingresado.length < 4) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Por favor, ingresa el código completo.',
                });
                return;
            }

            Swal.fire({
                title: 'Estamos verificando tu código...',
                text: 'Por favor espera.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '../backend/verificar_codigo_proveedor.php',
                type: 'POST',
                data: { codigo_verificacion: codigo_ingresado },
                dataType: 'json',
                success: function (response) {
                    Swal.close();

                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Registro Exitoso!',
                            text: response.message,
                        }).then(function () {
                            window.location = 'login_proveedor.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                        });
                    }
                },
                error: function () {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al verificar el código. Intenta de nuevo.',
                    });
                }
            });
        });

    </script>
</body>

</html>