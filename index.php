<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="shortcut icon" href="ico/comter.png" type="image/x-icon"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/8.1.0/mdb.min.css"/>
    <link rel="stylesheet" href="css/style.css" type="text/css"/>
    <link rel="stylesheet" href="css/styles-index.css">
    <title>Comter</title>
</head>
<body>
    <header class="header fixed-top" style="background-color:#1B419B;">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo">
                        <a href="index.php">
                            <img src="ico/comter.png" alt="Logo" style="width:50px"/>
                        </a>
                    </div>
                </div>
                <div class="col-lg-10">
                    <div class="header__nav__option">
                        <nav class="header__nav__menu mobile-menu">
                            <ul></ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>
<br><br><br>
    <div class="container page-content">
        <div class="row justify-content-center">
            <div class="col-12 col-md-4 mb-3">
                <label class="d-flex justify-content-center">
                    <input type="radio" name="engine" class="radio-input" onclick="window.location.href='frontend/login.php';"/>
                    <span class="radio-tile">
                        <div class="fix-cliente">
                            <div class="image-container cliente-card">
                                <img src="img/clientes.png" alt="Clientes"/>
                                <div class="centered-text">CLIENTE</div>
                            </div>
                        </div>
                    </span>
                </label>
            </div>
            <div class="col-12 col-md-4 mb-3">
                <label class="d-flex justify-content-center">
                    <input type="radio" name="engine" class="radio-input" onclick="window.location.href='frontend/login_proveedor.php';"/>
                    <span class="radio-tile">
                        <div class="image-container comter-card">
                            <img src="img/comter.png" alt="Comter"/>
                            <div class="centered-text">COMTER</div>
                        </div>
                    </span>
                </label>
            </div>
            <div class="col-12 col-md-4 mb-3">
                <label class="d-flex justify-content-center">
                    <input type="radio" name="engine" class="radio-input" onclick="window.location.href='frontend/admin.php';"/>
                    <span class="radio-tile">
                        <div class="image-container admin-card">
                            <img src="img/administrador.png" alt="Administrador"/>
                            <div class="centered-text">ADMIN</div>
                        </div>
                    </span>
                </label>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div>
                © <script>document.write(new Date().getFullYear());</script>, Comter |
                <a href="#" class="footer-link" style="color: #6c757d;">Osdems Digital Group</a>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/8.1.0/mdb.umd.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
