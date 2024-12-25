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
    <title>Comter</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;600;700;800&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #eee;
            color: #fff;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }


        nav.navbar {
            background-color: #e1e1e1;
        }


        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: calc(100vh - 120px);

        }

        .form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 350px;
            background-color: #fff;
            padding: 20px;
            border-radius: 20px;
            position: relative;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .title {
            font-size: 28px;
            color: royalblue;
            font-weight: 600;
            letter-spacing: -1px;
            position: relative;
            display: flex;
            align-items: center;
            padding-left: 30px;
        }

        /*.title::before,
        .title::after {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            border-radius: 50%;
            left: 0px;
            background-color: royalblue;
        }

        .title::before {
            width: 18px;
            height: 18px;
            background-color: royalblue;
        }

        .title::after {
            width: 18px;
            height: 18px;
            animation: pulse 1s linear infinite;
        }*/

        .message,
        .signin {
            color: rgba(88, 87, 87, 0.822);
            font-size: 14px;
        }

        .signin {
            text-align: center;
        }

        .signin a {
            color: royalblue;
        }

        .signin a:hover {
            text-decoration: underline royalblue;
        }

        .flex {
            display: flex;
            width: 100%;
            gap: 6px;
        }

        .form label {
            position: relative;
        }

        .form label .input {
            width: 100%;
            padding: 10px 10px 20px 10px;
            outline: 0;
            border: 1px solid rgba(105, 105, 105, 0.397);
            border-radius: 10px;
        }

        .form label .input+span {
            position: absolute;
            left: 10px;
            top: 15px;
            color: grey;
            font-size: 0.9em;
            cursor: text;
            transition: 0.3s ease;
        }

        .form label .input:placeholder-shown+span {
            top: 15px;
            font-size: 0.9em;
        }

        .form label .input:focus+span,
        .form label .input:valid+span {
            top: 0px;
            font-size: 0.7em;
            font-weight: 600;
        }

        .form label .input:valid+span {
            color: green;
        }

        .submit {
            border: none;
            outline: none;
            background-color: royalblue;
            padding: 10px;
            border-radius: 10px;
            color: #fff;
            font-size: 16px;
            transform: .3s ease;
        }

        .submit:hover {
            background-color: rgb(56, 90, 194);
            cursor: pointer;
        }

        @keyframes pulse {
            from {
                transform: scale(0.9);
                opacity: 1;
            }

            to {
                transform: scale(1.8);
                opacity: 0;
            }
        }

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
                                <li class="active"><a href="index.php">Home</a></li>
                                <li><a href="../index.php">Iniciar sesión</a></li>
                                <li><a href="registro.php">Regístrate aquí</a></li>

                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>

    <br><br><br><br><br><br><br><br>

    <div class="form-container">
        <form id="registro-form" class="form">
        <p class="title" style="display: flex; align-items: center; justify-content: flex-start; margin-left: -0px;">
    <!-- El icono de comter se ha hecho más pequeño y se alineará a la izquierda del texto -->
    <img src="../ico/comter.png" alt="" style="width:40px; height:auto; margin-right: 10px;">
    Registrarse
</p>


<p class="message text-justify" style="text-align: justify;">Regístrate ahora y obtén acceso completo a nuestra aplicación.</p>


            <div class="flex">
                <label for="firstname">
                    <input class="input" type="text" id="firstname" name="firstname" placeholder="" required="">
                    <span>Nombre</span>
                </label>

                <label for="lastname">
                    <input class="input" type="text" id="lastname" name="lastname" placeholder="" required="">
                    <span>Apellido</span>
                </label>
            </div>

            <label for="email">
                <input class="input" type="email" id="email" name="email" placeholder="" required=""
                    autocomplete="email">
                <span>Correo electrónico</span>
            </label>

            <label for="password">
                <input class="input" type="password" id="password" name="password" placeholder=""
                    autocomplete="new-password" required="">
                <span>Contraseña</span>
            </label>

            <label for="confirm_password">
                <input class="input" type="password" id="confirm_password" name="confirm_password" placeholder=""
                    autocomplete="new-password" required="">
                <span>Confirmar contraseña</span>
            </label>

            <label for="photo" class="form-label">
                <input class="input form-control" type="file" id="photo" name="photo" accept="image/*"
                    onchange="previewImage(event)">
            </label>

            <div id="photo-preview" class="text-center" style="margin-top: 10px; display: none;">
                <img id="preview-img" src="" alt="" style="max-width: 100px; max-height: 100px; border-radius: 10px;">
            </div>

            <button class="submit" type="submit">Registrarme</button>
            <p class="signin">¿Ya tienes una cuenta? <a href="../index.php">Iniciar sesión</a></p>
        </form>
    </div>
<br><br>

<footer class="content-footer footer" style="background-color:#edebea">
    <div class="container-xxl d-flex flex-wrap justify-content-center py-2 flex-md-row flex-column text-center" style="color:#838383;">
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



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/8.1.0/mdb.umd.min.js"></script>
    <script>
        $('#registro-form').submit(function (e) {
            e.preventDefault();


            Swal.fire({
                title: 'Estamos procesando tu registro...',
                html: 'Por favor espera mientras completamos el proceso.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            var formData = new FormData(this);

            $.ajax({
                url: '../backend/registrar_usuario.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {

                    Swal.close();

                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Registro Exitoso!',
                            text: response.message,
                        }).then(function () {
                            window.location = '../index.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                        });
                    }
                },
                error: function (xhr, status, error) {

                    Swal.close();

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al enviar el formulario. Intenta de nuevo.',
                    });
                }
            });
        });


        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function () {
                var preview = document.getElementById('photo-preview');
                var img = document.getElementById('preview-img');
                img.src = reader.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>


</body>

</html>