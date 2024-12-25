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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $inspection_date = $_POST['inspection_date'];
        $operators = $_POST['operators'];
        $descripcion = $_POST['descripcion'];
        $primer_t = $_POST['primer_t'] ?? 0;
        $segundo_t = $_POST['segundo_t'] ?? 0;
        $tercer_t = $_POST['tercer_t'] ?? 0;
        $burr = $_POST['burr'] ?? 0;
        $blockend_hole = $_POST['blockend_hole'] ?? 0;
        $non_flat_edge = $_POST['non_flat_edge'] ?? 0;
        $comments = $_POST['comments'] ?? '';
        $id_usuario = $_SESSION['id_usuarios'];

        $imagenes = $_FILES['imagenes'] ?? null;

        if (empty($inspection_date) || empty($operators) || empty($descripcion)) {
            $_SESSION['error_message_reporte'] = "Los campos 'Fecha de Inspección', 'Operadores' y 'Descripción' son obligatorios.";
        } else {
            $sql = "INSERT INTO report_fails (
                        inspection_date, operators, descripcion, primer_t, segundo_t, tercer_t, burr, blockend_hole, non_flat_edge, comments, id_usuarios
                    ) VALUES (
                        :inspection_date, :operators, :descripcion, :primer_t, :segundo_t, :tercer_t, :burr, :blockend_hole, :non_flat_edge, :comments, :id_usuarios
                    )";

            $stmt = $con->prepare($sql);
            $stmt->execute([
                ':inspection_date' => $inspection_date,
                ':operators' => $operators,
                ':descripcion' => $descripcion,
                ':primer_t' => $primer_t,
                ':segundo_t' => $segundo_t,
                ':tercer_t' => $tercer_t,
                ':burr' => $burr,
                ':blockend_hole' => $blockend_hole,
                ':non_flat_edge' => $non_flat_edge,
                ':comments' => $comments,
                ':id_usuarios' => $id_usuario
            ]);

            $report_id = $con->lastInsertId();

            if ($imagenes && $imagenes['error'][0] == 0) {
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

                foreach ($imagenes['name'] as $key => $image_name) {
                    $image_tmp_name = $imagenes['tmp_name'][$key];
                    $image_size = $imagenes['size'][$key];
                    $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);

                    if (!in_array(strtolower($image_ext), $allowed_extensions)) {
                        $_SESSION['error_message_reporte'] = "Solo se permiten imágenes con las extensiones: jpg, jpeg, png, gif.";
                        break;
                    }

                    if ($image_size > 5000000) {
                        $_SESSION['error_message_reporte'] = "El tamaño de la imagen es demasiado grande.";
                        break;
                    }

                    $image_data = file_get_contents($image_tmp_name);

                    $sql_image = "INSERT INTO report_images (id_report_fails, image) VALUES (:id_report_fails, :image)";
                    $stmt_image = $con->prepare($sql_image);
                    $stmt_image->execute([
                        ':id_report_fails' => $report_id,
                        ':image' => $image_data
                    ]);
                }
            }

            $_SESSION['message_reporte'] = "Datos insertados exitosamente.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message_reporte'] = "Error al insertar los datos: " . $e->getMessage();
    }
}

$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalRecordsQuery = "SELECT COUNT(*) FROM report_fails";
$stmt = $con->query($totalRecordsQuery);
$totalRecords = $stmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

$sql = "SELECT r.*, u.firstname, u.lastname, u.email
        FROM report_fails r
        JOIN usuarios u ON r.id_usuarios = u.id_usuarios
        LIMIT :offset, :limit";
$stmt = $con->prepare($sql);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql_images = "SELECT id_report_fails, image FROM report_images";
$stmt_images = $con->query($sql_images);
$images = $stmt_images->fetchAll(PDO::FETCH_ASSOC);


$image_map = [];
foreach ($images as $image) {
    $image_map[$image['id_report_fails']][] = base64_encode($image['image']);
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
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <!--<li>
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


                <div class="content-wrapper">
                    <div class="container mt-5">
                        <h1 class="text-center">Fails Report</h1>
                        <?php
                        date_default_timezone_set('America/Mexico_City');
                        $current_date = date('Y-m-d');
                        ?>
                        <form method="POST" class="mb-3" enctype="multipart/form-data">
                            <div class="row g-2">
                                <div class="col-md-2">
                                    <label for="inspection_date" class="form-label">Inspection Date</label>
                                    <input type="date" name="inspection_date" class="form-control" placeholder=""
                                        value="<?php echo $current_date; ?>" id="inspection_date">
                                </div>

                                <div class="col-md-2">
                                    <label for="operators" class="form-label">Operators</label>
                                    <input type="text" name="operators" class="form-control" placeholder="">
                                </div>

                                <div class="col-md-2">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea name="descripcion" class="form-control" placeholder=""></textarea>
                                </div>

                                <div class="col-md-1">
                                    <label for="primer_t" class="form-label">1er T</label>
                                    <input type="number" name="primer_t" class="form-control" placeholder="">
                                </div>
                                <div class="col-md-1">
                                    <label for="segundo_t" class="form-label">2do T</label>
                                    <input type="number" name="segundo_t" class="form-control" placeholder="">
                                </div>
                                <div class="col-md-1">
                                    <label for="tercer_t" class="form-label">3er T</label>
                                    <input type="number" name="tercer_t" class="form-control" placeholder="">
                                </div>

                                <div class="col-md-1">
                                    <label for="burr" class="form-label">Burr</label>
                                    <input type="number" name="burr" class="form-control" placeholder="">
                                </div>
                                <div class="col-md-1">
                                    <label for="blockend_hole" class="form-label">Blockend Hole</label>
                                    <input type="number" name="blockend_hole" class="form-control" placeholder="">
                                </div>
                                <div class="col-md-1">
                                    <label for="non_flat_edge" class="form-label">Non Flat Edge</label>
                                    <input type="number" name="non_flat_edge" class="form-control" placeholder="">
                                </div>

                                <div class="col-md-12 mt-2">
                                    <label for="comments" class="form-label">Comentarios</label>
                                    <textarea name="comments" class="form-control" placeholder=""></textarea>
                                </div>

                                <div class="col-md-2 mt-2">
                                    <label for="imagenes" class="form-label">Imágenes</label>
                                    <input type="file" name="imagenes[]" class="form-control" multiple>
                                    <small class="form-text text-muted">Selecciona varias imágenes para subir (máximo de
                                        archivos permitidos 5).</small>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-2"><i class="bx bx-plus"></i> Agregar
                                Fila</button>
                        </form>






                        <div>
                            <form action="../backend/exportar_excel_scc.php" method="post">
                                <button type="submit" class="btn btn-success" id="exportButton" disabled>
                                    <i class="bx bxs-file"></i> Exportar a Excel
                                </button>
                            </form>
                        </div>
                        <div class="navbar-nav align-items-start mt-3">
                            <div class="nav-item d-flex align-items-start">
                                <form method="GET" action="search_scc.php" class="d-flex align-items-start">
                                    <input type="text" name="search_scc" class="form-control border-0 shadow-none"
                                        placeholder="Buscar por Operators..." aria-label="Search..."
                                        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                                    <button type="submit" class="btn border-0 bg-transparent p-0 ms-2">
                                        <i class="bx bx-search fs-4 lh-0"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="container">

                        <div class="table-responsive mt-3">
                            <?php
                            if (isset($_SESSION['message_reporte'])) {
                                echo '<div class="alert alert-success">' . $_SESSION['message_reporte'] . '</div>';
                                unset($_SESSION['message_reporte']);
                            }

                            if (isset($_SESSION['error_message_reporte'])) {
                                echo '<div class="alert alert-danger">' . $_SESSION['error_message_reporte'] . '</div>';
                                unset($_SESSION['error_message_reporte']);
                            }
                            ?>
                            <table class="table table-bordered" id="inspectionTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Inspection Date</th>
                                        <th>Operators</th>
                                        <th>Descripción</th>
                                        <th>1 er T</th>
                                        <th>2 do T</th>
                                        <th>3 er T</th>
                                        <th>GOODS</th>
                                        <th>Burr</th>
                                        <th>Blockend Hole</th>
                                        <th>Non Flat Edge</th>
                                        <th>Total</th>
                                        <th>Total Final</th>
                                        <th>Yield</th>
                                        <th>Comments</th>
                                        <th>Imagen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (count($rows) > 0):
                                        $previousDate = null;
                                        foreach ($rows as $row):
                                            $date = new DateTime($row['inspection_date']);
                                            $formattedDate = $date->format('d/m/Y');
                                            $dayOfWeek = $date->format('l');
                                            $daysInSpanish = [
                                                'Monday' => 'Lunes',
                                                'Tuesday' => 'Martes',
                                                'Wednesday' => 'Miércoles',
                                                'Thursday' => 'Jueves',
                                                'Friday' => 'Viernes',
                                                'Saturday' => 'Sábado',
                                                'Sunday' => 'Domingo'
                                            ];
                                            $dayInSpanish = $daysInSpanish[$dayOfWeek];

                                            if ($previousDate !== null && $previousDate !== $formattedDate): ?>
                                                <tr class="date-separator">
                                                    <td colspan="22"
                                                        style="background-color: #f2f2f2; text-align: center; font-weight: bold;">
                                                        Cambio de fecha: <?= $formattedDate ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <td><?= $formattedDate . ' ' . $dayInSpanish ?></td>
                                                <td><?= htmlspecialchars($row['operators']) ?></td>
                                                <td><?= nl2br(htmlspecialchars($row['descripcion'])) ?></td>
                                                <td><?= htmlspecialchars($row['primer_t']) ?></td>
                                                <td><?= htmlspecialchars($row['segundo_t']) ?></td>
                                                <td><?= htmlspecialchars($row['tercer_t']) ?></td>
                                                <td><?= htmlspecialchars($row['primer_t'] + $row['segundo_t'] + $row['tercer_t']) ?>
                                                </td>
                                                <td><?= htmlspecialchars($row['burr']) ?></td>
                                                <td><?= htmlspecialchars($row['blockend_hole']) ?></td>
                                                <td><?= htmlspecialchars($row['non_flat_edge']) ?></td>
                                                <td><?= htmlspecialchars($row['burr'] + $row['blockend_hole'] + $row['non_flat_edge']) ?>
                                                </td>
                                                <td><?= htmlspecialchars(
                                                    ($row['primer_t'] + $row['segundo_t'] + $row['tercer_t']) +
                                                    ($row['burr'] + $row['blockend_hole'] + $row['non_flat_edge'])
                                                ) ?></td>
                                                <td>
                                                    <?php
                                                    $numerator = $row['primer_t'] + $row['segundo_t'] + $row['tercer_t'];
                                                    $denominator = $numerator + $row['burr'] + $row['blockend_hole'] + $row['non_flat_edge'];
                                                    if ($denominator != 0) {
                                                        $yield = ($numerator / $denominator) * 100;
                                                        echo '<strong>' . round($yield) . '%</strong>';
                                                    } else {
                                                        echo '<strong>N/A</strong>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if (empty($row['comments'])) {
                                                        echo 'N/A';
                                                    } else {
                                                        echo nl2br(htmlspecialchars($row['comments']));
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if (isset($image_map[$row['id_report_fails']])) {
                                                        foreach ($image_map[$row['id_report_fails']] as $encoded_image) {
                                                            echo '<img src="data:image/jpeg;base64,' . $encoded_image . '" alt="Imagen" width="100" /> ';
                                                        }
                                                    } else {
                                                        echo 'No image';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                            $previousDate = $formattedDate;
                                        endforeach;
                                    else: ?>
                                        <tr>
                                            <td colspan="22" class="text-center">No hay registros disponibles</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

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
                        </div>
                    </div>

                    <style>
                        .date-separator td {
                            background-color: #f2f2f2;
                            text-align: center;
                            font-weight: bold;
                            color: #333;
                        }
                    </style>



                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            var table = document.getElementById('inspectionTable');
                            var exportButton = document.getElementById('exportButton');


                            var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
                            var hasRecords = rows.length > 0 && rows[0].getElementsByTagName('td').length > 1;


                            exportButton.disabled = !hasRecords;
                        });
                    </script>





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