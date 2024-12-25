<?php
session_start();
require '../../config/connection.php';

if (!isset($_SESSION['id_usuarios'])) {
    echo 'No has iniciado sesión o tu sesión ha expirado.';
    header('Location: ../../login.php');
    exit();
}

$id_usuario = $_SESSION['id_usuarios'];
$db = new Database();
$con = $db->conectar();

$sql = "SELECT firstname, lastname, role, photo, active FROM usuarios WHERE id_usuarios = :id_usuario";
$stmt = $con->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $role = $row['role'];
    $photo = $row['photo'];
    $active = $row['active'];

    if ((int) $active === 0) {
        echo 'Tu cuenta ha sido desactivada. Por favor, contacta con el administrador.';
        header('Location: ../../index.php');
        exit();
    }

    if ($role !== 'Proveedor') {
        echo 'No tienes permiso para acceder a esta página.';
        header('Location: ../../index.php');
        exit();
    }

    if ($photo !== null) {
        $base64Image = base64_encode($photo);
        $imageSrc = 'data:image/jpeg;base64,' . $base64Image;
    } else {
        $imageSrc = '../../assets/img/avatars/1.png';
    }
} else {
    echo 'Usuario no encontrado o cuenta no válida.';
    header('Location: ../../index.php');
    exit();
}
if (isset($_POST['add_record'])) {
    try {

        $comentarios_defecto = isset($_POST['comentarios_defecto']) ? $_POST['comentarios_defecto'] : null;

        $sql_insert = "INSERT INTO reporte (folio_captura, folio_requisicion, cliente_fabricante, fecha_reporte, caja, po_skid, num_parte, date_code, descripcion, nombre_operador, horario, productividad_a, productividad_b, total_inspeccionadas, defectos_y_descripcion, total_defectos, buenas, comentarios_defecto, total_inspeccionadas_c, comentarios_descripcion_sorteo, id_usuario)
                       VALUES (:folio_captura, :folio_requisicion, :cliente_fabricante, :fecha_reporte, :caja, :po_skid, :num_parte, :date_code, :descripcion, :nombre_operador, :horario, :productividad_a, :productividad_b, :total_inspeccionadas, :defectos_y_descripcion, :total_defectos, :buenas, :comentarios_defecto, :total_inspeccionadas_c, :comentarios_descripcion_sorteo, :id_usuario)";

        $stmt_insert = $con->prepare($sql_insert);
        $stmt_insert->execute([
            ':folio_captura' => $_POST['folio_captura'],
            ':folio_requisicion' => $_POST['folio_requisicion'],
            ':cliente_fabricante' => $_POST['cliente_fabricante'],
            ':fecha_reporte' => $_POST['fecha_reporte'],
            ':caja' => $_POST['caja'],
            ':po_skid' => $_POST['po_skid'],
            ':num_parte' => $_POST['num_parte'],
            ':date_code' => $_POST['date_code'],
            ':descripcion' => $_POST['descripcion'],
            ':nombre_operador' => $_POST['nombre_operador'],
            ':horario' => $_POST['horario'],
            ':productividad_a' => $_POST['productividad_a'],
            ':productividad_b' => $_POST['productividad_b'],
            ':total_inspeccionadas' => $_POST['total_inspeccionadas'],
            ':defectos_y_descripcion' => $_POST['defectos_y_descripcion'],
            ':total_defectos' => $_POST['total_defectos'],
            ':buenas' => $_POST['buenas'],
            ':comentarios_defecto' => $comentarios_defecto,
            ':total_inspeccionadas_c' => $_POST['total_inspeccionadas_c'],
            ':comentarios_descripcion_sorteo' => $_POST['comentarios_descripcion_sorteo'],
            ':id_usuario' => $id_usuario
        ]);
        $_SESSION['message_reporte'] = "Datos insertados exitosamente.";
    } catch (PDOException $e) {
        $_SESSION['error_message_reporte'] = "Error al insertar los datos: " . $e->getMessage();
    }
}


$totalPages = 1;


$search = isset($_GET['search_reporte']) ? trim($_GET['search_reporte']) : '';

$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;


if (empty($search)) {

    $records = [];


    $totalRecords = 0;
} else {

    $sqlCount = "SELECT COUNT(*) FROM reporte WHERE cliente_fabricante LIKE :search";
    $stmtCount = $con->prepare($sqlCount);
    $stmtCount->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    $stmtCount->execute();
    $totalRecords = $stmtCount->fetchColumn();


    $sql = "SELECT r.*, u.firstname, u.lastname, u.email
            FROM reporte r
            JOIN usuarios u ON r.id_usuario = u.id_usuarios
            WHERE r.cliente_fabricante LIKE :search
            LIMIT :limit OFFSET :offset";


    $stmt = $con->prepare($sql);
    $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


$totalPages = ceil($totalRecords / $limit);


if ($totalRecords == 0) {
    $totalPages = 1;
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="../../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../../assets/css/demo.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../../assets/vendor/libs/apex-charts/apex-charts.css" />
    <script src="../../assets/vendor/js/helpers.js"></script>
    <script src="../../assets/js/config.js"></script>
    <link rel="shortcut icon" href="../ico/comter.png" type="image/x-icon">
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
    <title>Proveedor</title>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">


            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="../admin_panel.php" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <img src="../ico/comter.png" alt="" width="50">
                            <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                                    <g id="Icon" transform="translate(27.000000, 15.000000)">
                                        <g id="Mask" transform="translate(0.000000, 8.000000)">
                                            <mask id="mask-2" fill="white">
                                                <use xlink:href="#path-1"></use>
                                            </mask>
                                            <use fill="#696cff" xlink:href="#path-1"></use>
                                            <g id="Path-3" mask="url(#mask-2)">
                                                <use fill="#696cff" xlink:href="#path-3"></use>
                                                <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                                            </g>
                                            <g id="Path-4" mask="url(#mask-2)">
                                                <use fill="#696cff" xlink:href="#path-4"></use>
                                                <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                                            </g>
                                        </g>
                                        <g id="Triangle"
                                            transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                                            <use fill="#696cff" xlink:href="#path-5"></use>
                                            <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                                        </g>
                                    </g>
                                </g>
                            </g>
                            </svg>
                        </span>
                        <span class="app-brand-text demo menu-text fw-bolder ms-2">COMTER</span>
                    </a>

                    <a href="javascript:void(0);"
                        class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">

                    <li class="menu-item active">
                        <a href="../admin_panel.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Analytics">Dashboard</div>
                        </a>
                    </li>


                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-layout"></i>
                            <div data-i18n="Layouts">Reportes</div>
                        </a>

                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="infinex.php" class="menu-link">
                                    <div data-i18n="Without menu">Infinex</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="molex.php" class="menu-link">
                                    <div data-i18n="Without navbar">Molex SEM46</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="reporte.php" class="menu-link">
                                    <div data-i18n="Container">Reporte</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="scc.php" class="menu-link">
                                    <div data-i18n="Fluid">SCC</div>
                                </a>
                            </li>
                            <!--<li class="menu-item">
                <a href="layouts-blank.html" class="menu-link">
                  <div data-i18n="Blank">Blank</div>
                </a>
              </li>-->
                        </ul>
                    </li>


            </aside>

            <div class="layout-page">

                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <form method="GET" action="search_molex.php" class="d-flex align-items-center">
                                    <input type="text" name="search" class="form-control border-0 shadow-none"
                                        placeholder="Buscar por Operador..." aria-label="Search..."
                                        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                                    <button type="submit" class="btn border-0 bg-transparent p-0 ms-2">
                                        <i class="bx bx-search fs-4 lh-0"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <ul class="navbar-nav flex-row align-items-center ms-auto">




                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="<?php echo $imageSrc; ?>" alt=""
                                            class="w-px-40 h-auto rounded-circle" />
                                    </div>



                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="<?php echo $imageSrc; ?>" alt=""
                                                            class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span
                                                        class="fw-semibold d-block"><?php echo $firstname . ' ' . $lastname; ?></span>
                                                    <small class="text-muted"><?php echo $role; ?></small>
                                                </div>


                                            </div>
                                        </a>
                                    </li>
                                    <!--<li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">My Profile</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bx bx-cog me-2"></i>
                                            <span class="align-middle">Settings</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <span class="d-flex align-items-center align-middle">
                                                <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                                                <span class="flex-grow-1 align-middle">Billing</span>
                                                <span
                                                    class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>-->
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="confirmLogout()">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Cerrar sesión</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                        </ul>
                    </div>
                </nav>


                <div class="container mt-5">
                    <h2 class="text-center">Agregar y Visualizar Registros</h2>
                    <?php if (isset($_SESSION['message_reporte'])): ?>
                        <div class="alert alert-success">
                            <?php echo $_SESSION['message_reporte'];
                            unset($_SESSION['message_reporte']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error_message_reporte'])): ?>
                        <div class="alert alert-danger">
                            <?php echo $_SESSION['error_message_reporte'];
                            unset($_SESSION['error_message_reporte']); ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="folio_captura" class="form-label">Folio de Captura</label>
                                <input type="text" class="form-control" id="folio_captura" name="folio_captura"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <label for="folio_requisicion" class="form-label">Folio de Requisición</label>
                                <input type="text" class="form-control" id="folio_requisicion" name="folio_requisicion"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <label for="cliente_fabricante" class="form-label">Cliente/Fabricante</label>
                                <input type="text" class="form-control" id="cliente_fabricante"
                                    name="cliente_fabricante" required>
                            </div>
                            <div class="col-md-3">
                                <label for="fecha_reporte" class="form-label">Fecha del Reporte</label>
                                <input type="date" class="form-control" id="fecha_reporte" name="fecha_reporte"
                                    required>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-2">
                                <label for="caja" class="form-label">Caja</label>
                                <input type="text" class="form-control" id="caja" name="caja" required>
                            </div>
                            <div class="col-md-2">
                                <label for="po_skid" class="form-label">PO # o Skid</label>
                                <input type="text" class="form-control" id="po_skid" name="po_skid" required>
                            </div>
                            <div class="col-md-2">
                                <label for="num_parte" class="form-label">Número de Parte</label>
                                <input type="text" class="form-control" id="num_parte" name="num_parte" required>
                            </div>
                            <div class="col-md-2">
                                <label for="date_code" class="form-label">Date Code Lote</label>
                                <input type="text" class="form-control" id="date_code" name="date_code" required>
                            </div>
                            <div class="col-md-4">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-3">
                                <label for="nombre_operador" class="form-label">Nombre del Operador</label>
                                <input type="text" class="form-control" id="nombre_operador" name="nombre_operador"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <label for="horario" class="form-label">Horario</label>
                                <input type="text" class="form-control" id="horario" name="horario" required>
                            </div>
                            <div class="col-md-2">
                                <label for="productividad_a" class="form-label">Productividad A</label>
                                <input type="number" class="form-control" id="productividad_a" name="productividad_a"
                                    required>
                            </div>
                            <div class="col-md-2">
                                <label for="productividad_b" class="form-label">Productividad B</label>
                                <input type="number" class="form-control" id="productividad_b" name="productividad_b"
                                    required>
                            </div>
                            <div class="col-md-2">
                                <label for="total_inspeccionadas" class="form-label">Total Inspeccionadas</label>
                                <input type="number" class="form-control" id="total_inspeccionadas"
                                    name="total_inspeccionadas" required>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label for="defectos_y_descripcion" class="form-label">Defectos y Descripción</label>
                                <textarea class="form-control" id="defectos_y_descripcion" name="defectos_y_descripcion"
                                    rows="2" required></textarea>
                            </div>
                            <div class="col-md-2">
                                <label for="total_defectos" class="form-label">Total Defectos</label>
                                <input type="number" class="form-control" id="total_defectos" name="total_defectos"
                                    required>
                            </div>
                            <div class="col-md-2">
                                <label for="buenas" class="form-label">Buenas</label>
                                <input type="number" class="form-control" id="buenas" name="buenas" required>
                            </div>
                            <div class="col-md-2">
                                <label for="total_inspeccionadas_c" class="form-label">Total Inspeccionadas C</label>
                                <input type="number" class="form-control" id="total_inspeccionadas_c"
                                    name="total_inspeccionadas_c" required>
                            </div>
                            <div class="col-md-6">
                                <label for="comentarios_descripcion_sorteo" class="form-label">Comentarios Descripción
                                    Sorteo</label>
                                <textarea class="form-control" id="comentarios_descripcion_sorteo"
                                    name="comentarios_descripcion_sorteo" rows="2" required></textarea>
                            </div>


                        </div>
                        <button type="submit" name="add_record" class="btn btn-primary mt-3">Agregar</button>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            var table = document.getElementById('inspectionTable');
                            var exportButton = document.getElementById('exportButton');


                            var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
                            var hasRecords = rows.length > 0 && rows[0].getElementsByTagName('td').length > 1;


                            exportButton.disabled = !hasRecords;
                        });
                    </script>

                    <div>
                        <form action="../backend/exportar_excel_reporte.php" method="post">
                            <button type="submit" class="btn btn-success" id="exportButton" disabled>
                                <i class="bx bxs-file"></i> Exportar a Excel
                            </button>
                        </form>
                    </div>
                    <div class="navbar-nav align-items-start mt-3">
                        <div class="nav-item d-flex align-items-start">
                            <form method="GET" action="search_reporte.php" class="d-flex align-items-start">
                                <input type="text" name="search_reporte" class="form-control border-0 shadow-none"
                                    placeholder="Buscar por Cliente/Fabricante..." aria-label="Search..."
                                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                                <button type="submit" class="btn border-0 bg-transparent p-0 ms-2">
                                    <i class="bx bx-search fs-4 lh-0"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <h3 class="mt-4">Registros</h3>
                    <div class="table-responsive">
                        <table id="inspectionTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Folio Captura</th>
                                    <th>Folio Requerimiento de Servicio</th>
                                    <th>Cliente/Fabricante</th>
                                    <th>Fecha Reporte</th>
                                    <th>Caja</th>
                                    <th>PO/Skid</th>
                                    <th>Número de Parte</th>
                                    <th>Date Code. Lote o Fecha de Fabricación</th>
                                    <th>Descripción ¿Qué es PCB, Housing, Conector, etc?</th>
                                    <th>Nombre del Operador</th>
                                    <th>Horario</th>
                                    <th>Horas o Minutos x el Total de Trabajadores. A</th>
                                    <th>Rate Meta Unidades x Hora. B</th>
                                    <th>Total Inspeccionadas.</th>
                                    <th>Rate Real, Unidades Obtenidas por Hora</th>
                                    <th>Diferencia se alcanzó el rate "ok"</th>
                                    <th>Total Defectos</th>
                                    <th>Total Inspeccionadas. C</th>
                                    <th>Comentarios o Descripción del Defecto que Inició el Sorteo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($search) && empty($records)): ?>

                                    <tr>
                                        <td colspan="21" class="text-center">Por favor, ingrese un nombre para buscar.</td>
                                    </tr>
                                <?php elseif (empty($records)): ?>

                                    <tr>
                                        <td colspan="21" class="text-center">No se encontraron resultados para la búsqueda:
                                            "<?= htmlspecialchars($search) ?>"</td>
                                    </tr>
                                <?php else: ?>

                                    <?php foreach ($records as $record): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($record['folio_captura']) ?></td>
                                            <td><?= htmlspecialchars($record['folio_requisicion']) ?></td>
                                            <td><?= htmlspecialchars($record['cliente_fabricante']) ?></td>
                                            <td><?= (new DateTime($record['fecha_reporte']))->format('d/m/Y') ?></td>
                                            <td><?= htmlspecialchars($record['caja']) ?></td>
                                            <td><?= htmlspecialchars($record['po_skid']) ?></td>
                                            <td><?= htmlspecialchars($record['num_parte']) ?></td>
                                            <td><?= htmlspecialchars($record['date_code']) ?></td>
                                            <td><?= htmlspecialchars($record['descripcion']) ?></td>
                                            <td><?= htmlspecialchars($record['nombre_operador']) ?></td>
                                            <td><?= htmlspecialchars($record['horario']) ?></td>
                                            <td><?= htmlspecialchars($record['productividad_a']) ?></td>
                                            <td><?= htmlspecialchars($record['productividad_b']) ?></td>
                                            <td><?= htmlspecialchars($record['total_inspeccionadas']) ?></td>
                                            <td><?= htmlspecialchars($record['defectos_y_descripcion']) ?></td>
                                            <td><?= htmlspecialchars($record['total_defectos']) ?></td>
                                            <td><?= htmlspecialchars($record['buenas']) ?></td>
                                            <td><?= htmlspecialchars($record['total_inspeccionadas_c']) ?></td>
                                            <td><?= htmlspecialchars($record['comentarios_descripcion_sorteo']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination">
                        <ul class="pagination">
                            <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page - 1 ?>">Anterior</a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $page == $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1 ?>">Siguiente</a>
                            </li>
                        </ul>
                    </div>


                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl d-flex flex-column flex-md-row justify-content-center py-2">
                            <div class="mb-2 mb-md-0 text-center text-md-start">
                                ©
                                <script>
                                    document.write(new Date().getFullYear());
                                </script>
                                , Comter
                                <a href="#" class="footer-link fw-bolder">Osdems Digital Group</a>
                            </div>
                        </div>
                    </footer>

                    <div class="content-backdrop fade"></div>
                </div>

            </div>

        </div>


        <div class="layout-overlay layout-menu-toggle"></div>
    </div>




    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../assets/vendor/js/menu.js"></script>
    <script src="../../assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/dashboards-analytics.js"></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var table = document.getElementById('inspectionTable');
            var exportButton = document.getElementById('exportButton');


            var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            var hasRecords = rows.length > 0 && rows[0].getElementsByTagName('td').length > 1;


            exportButton.disabled = !hasRecords;
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

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
                    window.location.href = '../backend/home/logout.php';
                }
            });
        }
    </script>
</body>

</html>